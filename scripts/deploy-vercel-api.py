#!/usr/bin/env python3
"""
Deploy both Laravel projects to Vercel using ONLY the Vercel REST API.
No Vercel CLI needed, no browser login needed.

Usage:
    export VERCEL_TOKEN="vercel_xxxxxxxxxxxxxxxxxxxxxx"
    python3 scripts/deploy-vercel-api.py

Or pass the token as an argument:
    python3 scripts/deploy-vercel-api.py vercel_xxxxxxxxxxxxxxxxxxxxxx

The script will:
1. Verify the Vercel token and get user info
2. Create two Vercel projects (elegance-store, elegance-api)
3. Set all required environment variables for each project
4. Deploy the projects from the GitHub repo
5. Print the final URLs

Requirements:
- VERCEL_TOKEN environment variable (Vercel API token from https://vercel.com/account/tokens)
- The GitHub repo must already be connected to Vercel OR Vercel can pull from a public GitHub repo
"""
import json
import os
import sys
import time
import requests
from pathlib import Path

# ============================================================
# CONFIGURATION
# ============================================================
VERCEL_TOKEN = sys.argv[1] if len(sys.argv) > 1 else os.environ.get("VERCEL_TOKEN", "")

# Supabase project (already created and seeded)
SUPABASE_DB_URL = "postgresql://postgres.ofehrwapsjlfoersvxet:EleganceShop2026%21Secure@aws-1-eu-central-1.pooler.supabase.com:6543/postgres"

# Laravel APP_KEY for each project
FRONTEND_APP_KEY = "base64:BOdLkv68u7lm8ZDC4QyuRUdB7q5qYtyBy/qc934NHrM="
BACKEND_APP_KEY = "base64:GpG7ZNM/+Kx7Tq+0KpDhDdw/CohOY9hHJXtwyg9UI04="

# GitHub repo info
GITHUB_REPO = "Mahmoudalabsi/eshop-laravel"
GITHUB_BRANCH = "main"

# Vercel project names
FRONTEND_PROJECT = "elegance-store"
BACKEND_PROJECT = "elegance-api"

HEADERS = {
    "Authorization": f"Bearer {VERCEL_TOKEN}",
    "Content-Type": "application/json",
}

# ============================================================
# HELPERS
# ============================================================
def print_step(msg): print(f"\n▶ {msg}")
def print_ok(msg):   print(f"  ✓ {msg}")
def print_warn(msg): print(f"  ⚠ {msg}")
def print_err(msg):  print(f"  ✗ {msg}")

def api_call(method, endpoint, **kwargs):
    """Make a Vercel API call and return parsed JSON or error."""
    url = f"https://api.vercel.com{endpoint}"
    try:
        resp = requests.request(method, url, headers=HEADERS, timeout=30, **kwargs)
        if resp.status_code < 300:
            try:
                return resp.json(), None
            except Exception:
                return {"raw": resp.text}, None
        else:
            err = f"HTTP {resp.status_code}: {resp.text[:300]}"
            return None, err
    except Exception as e:
        return None, str(e)

# ============================================================
# MAIN
# ============================================================
if not VERCEL_TOKEN:
    print("ERROR: VERCEL_TOKEN not provided.")
    print("Get one from: https://vercel.com/account/tokens")
    print("Then run: VERCEL_TOKEN=vercel_xxx python3 scripts/deploy-vercel-api.py")
    sys.exit(1)

print("=" * 60)
print("🚀 نشر Elegance Fashion على Vercel عبر API")
print("=" * 60)
print(f"\n  Supabase DB: elegance-prod (eu-central-1)")
print(f"  GitHub repo: {GITHUB_REPO}")

# ============================================================
# STEP 1: Verify Vercel token
# ============================================================
print_step("الخطوة 1: التحقق من Vercel token...")
user, err = api_call("GET", "/v2/user")
if err:
    print_err(f"فشل التحقق من التوكن: {err}")
    sys.exit(1)
print_ok(f"مُسجّل كـ: {user.get('user', {}).get('email', 'unknown')}")

# ============================================================
# STEP 2: Create Backend project
# ============================================================
print_step(f"الخطوة 2: إنشاء مشروع {BACKEND_PROJECT}...")

# First check if project exists
existing, _ = api_call("GET", f"/v9/projects/{BACKEND_PROJECT}")
if existing and "id" in existing:
    print_warn(f"المشروع {BACKEND_PROJECT} موجود مسبقاً - سيتم استخدامه")
    backend_project = existing
else:
    # Create new project linked to GitHub repo
    payload = {
        "name": BACKEND_PROJECT,
        "framework": None,
        "gitRepository": {
            "type": "github",
            "repo": GITHUB_REPO,
        },
        "rootDirectory": "ecommerce-shop",
        "buildCommand": "bash vercel-build.sh",
        "outputDirectory": "public",
        "installCommand": "npm install --no-audit --no-fund --omit=optional",
    }
    backend_project, err = api_call("POST", "/v11/projects", json=payload)
    if err:
        print_err(f"فشل إنشاء مشروع {BACKEND_PROJECT}: {err}")
        print("  يمكنك إنشاؤه يدوياً من Vercel Dashboard وإعادة تشغيل هذا السكربت")
    else:
        print_ok(f"تم إنشاء مشروع: {backend_project.get('name')}")

backend_id = backend_project.get("id") if backend_project else None

# ============================================================
# STEP 3: Set Backend env vars
# ============================================================
if backend_id:
    print_step("الخطوة 3: ضبط متغيرات البيئة للـ Backend...")
    backend_envs = [
        ("APP_NAME", "Elegance Fashion API"),
        ("APP_ENV", "production"),
        ("APP_KEY", BACKEND_APP_KEY),
        ("APP_DEBUG", "false"),
        ("APP_LOCALE", "ar"),
        ("APP_FALLBACK_LOCALE", "en"),
        ("DB_CONNECTION", "pgsql"),
        ("DB_URL", SUPABASE_DB_URL),
        ("RUN_MIGRATIONS_ON_BOOT", "true"),
        ("SESSION_DRIVER", "cookie"),
        ("CACHE_STORE", "array"),
        ("QUEUE_CONNECTION", "sync"),
        ("FILESYSTEM_DISK", "local"),
        ("LOG_CHANNEL", "stderr"),
        ("LOG_LEVEL", "warning"),
        ("SANCTUM_STATEFUL_DOMAINS", "*"),
        ("VIEW_COMPILED_PATH", "/tmp/storage/framework/views"),
        ("APP_CONFIG_CACHE", "/tmp/config.php"),
        ("APP_EVENTS_CACHE", "/tmp/events.php"),
        ("APP_ROUTES_CACHE", "/tmp/routes.php"),
    ]
    for key, value in backend_envs:
        payload = {
            "key": key,
            "value": value,
            "type": "encrypted",
            "target": ["production", "preview", "development"],
        }
        _, err = api_call("POST", f"/v10/projects/{backend_id}/env", json=payload)
        if err:
            print_warn(f"  {key}: {err[:80]}")
        else:
            print_ok(f"  {key}")

# ============================================================
# STEP 4: Deploy Backend
# ============================================================
if backend_id:
    print_step("الخطوة 4: نشر الـ Backend...")
    payload = {
        "name": BACKEND_PROJECT,
        "project": backend_id,
        "target": "production",
        "gitSource": {
            "type": "github",
            "repo": GITHUB_REPO,
            "ref": GITHUB_BRANCH,
        },
    }
    deploy, err = api_call("POST", "/v13/deployments", json=payload)
    if err:
        print_err(f"فشل بدء النشر: {err}")
    else:
        deploy_id = deploy.get("id")
        print_ok(f"بدأ النشر (ID: {deploy_id[:12]}...)")
        print("  في انتظار اكتمال البناء... (قد يستغرق 3-5 دقائق)")
        # Poll for completion
        for i in range(60):
            time.sleep(10)
            status, _ = api_call("GET", f"/v13/deployments/{deploy_id}")
            if status:
                state = status.get("status", "unknown")
                ready = status.get("readyState", "unknown")
                print(f"  [{i*10}s] state={ready}")
                if ready in ("READY", "ERROR", "CANCELED"):
                    break
        if status and status.get("readyState") == "READY":
            backend_url = f"https://{status.get('alias', [BACKEND_PROJECT + '.vercel.app'])[0]}"
            print_ok(f"تم نشر الـ Backend: {backend_url}")
        else:
            print_err(f"فشل النشر: {status.get('readyState') if status else 'unknown'}")
            backend_url = f"https://{BACKEND_PROJECT}.vercel.app"

# ============================================================
# STEP 5: Create Frontend project
# ============================================================
print_step(f"الخطوة 5: إنشاء مشروع {FRONTEND_PROJECT}...")
existing, _ = api_call("GET", f"/v9/projects/{FRONTEND_PROJECT}")
if existing and "id" in existing:
    print_warn(f"المشروع {FRONTEND_PROJECT} موجود مسبقاً - سيتم استخدامه")
    frontend_project = existing
else:
    payload = {
        "name": FRONTEND_PROJECT,
        "framework": None,
        "gitRepository": {
            "type": "github",
            "repo": GITHUB_REPO,
        },
        "rootDirectory": "ecommerce-eshop",
        "buildCommand": "bash vercel-build.sh",
        "outputDirectory": "public",
        "installCommand": "npm install --no-audit --no-fund --omit=optional",
    }
    frontend_project, err = api_call("POST", "/v11/projects", json=payload)
    if err:
        print_err(f"فشل إنشاء مشروع {FRONTEND_PROJECT}: {err}")
    else:
        print_ok(f"تم إنشاء مشروع: {frontend_project.get('name')}")

frontend_id = frontend_project.get("id") if frontend_project else None

# ============================================================
# STEP 6: Set Frontend env vars
# ============================================================
if frontend_id:
    print_step("الخطوة 6: ضبط متغيرات البيئة للواجهة...")
    frontend_envs = [
        ("APP_NAME", "Elegance Fashion Store"),
        ("APP_ENV", "production"),
        ("APP_KEY", FRONTEND_APP_KEY),
        ("APP_DEBUG", "false"),
        ("APP_LOCALE", "ar"),
        ("APP_FALLBACK_LOCALE", "en"),
        ("DB_CONNECTION", "pgsql"),
        ("DB_URL", SUPABASE_DB_URL),
        ("RUN_MIGRATIONS_ON_BOOT", "true"),
        ("API_BASE_URL", f"https://{BACKEND_PROJECT}.vercel.app/api/v1"),
        ("API_TIMEOUT", "15"),
        ("API_RETRIES", "2"),
        ("SESSION_DRIVER", "cookie"),
        ("CACHE_STORE", "array"),
        ("QUEUE_CONNECTION", "sync"),
        ("FILESYSTEM_DISK", "local"),
        ("LOG_CHANNEL", "stderr"),
        ("LOG_LEVEL", "warning"),
        ("SANCTUM_STATEFUL_DOMAINS", "*"),
        ("VIEW_COMPILED_PATH", "/tmp/storage/framework/views"),
        ("APP_CONFIG_CACHE", "/tmp/config.php"),
        ("APP_EVENTS_CACHE", "/tmp/events.php"),
        ("APP_ROUTES_CACHE", "/tmp/routes.php"),
    ]
    for key, value in frontend_envs:
        payload = {
            "key": key,
            "value": value,
            "type": "encrypted",
            "target": ["production", "preview", "development"],
        }
        _, err = api_call("POST", f"/v10/projects/{frontend_id}/env", json=payload)
        if err:
            print_warn(f"  {key}: {err[:80]}")
        else:
            print_ok(f"  {key}")

# ============================================================
# STEP 7: Deploy Frontend
# ============================================================
if frontend_id:
    print_step("الخطوة 7: نشر الواجهة...")
    payload = {
        "name": FRONTEND_PROJECT,
        "project": frontend_id,
        "target": "production",
        "gitSource": {
            "type": "github",
            "repo": GITHUB_REPO,
            "ref": GITHUB_BRANCH,
        },
    }
    deploy, err = api_call("POST", "/v13/deployments", json=payload)
    if err:
        print_err(f"فشل بدء النشر: {err}")
    else:
        deploy_id = deploy.get("id")
        print_ok(f"بدأ النشر (ID: {deploy_id[:12]}...)")
        print("  في انتظار اكتمال البناء... (قد يستغرق 3-5 دقائق)")
        for i in range(60):
            time.sleep(10)
            status, _ = api_call("GET", f"/v13/deployments/{deploy_id}")
            if status:
                ready = status.get("readyState", "unknown")
                print(f"  [{i*10}s] state={ready}")
                if ready in ("READY", "ERROR", "CANCELED"):
                    break
        if status and status.get("readyState") == "READY":
            frontend_url = f"https://{status.get('alias', [FRONTEND_PROJECT + '.vercel.app'])[0]}"
            print_ok(f"تم نشر الواجهة: {frontend_url}")
        else:
            print_err(f"فشل النشر")

# ============================================================
# STEP 8: Summary
# ============================================================
print("\n" + "=" * 60)
print("✅ تم النشر!")
print("=" * 60)
print(f"\n  🛍️  المتجر:    https://{FRONTEND_PROJECT}.vercel.app")
print(f"  📡  الـ API:    https://{BACKEND_PROJECT}.vercel.app")
print(f"  🗄️  قاعدة البيانات: elegance-prod (Supabase)")
print("\n  حساب الأدمن:")
print(f"    البريد:      admin@elegance.com")
print(f"    كلمة المرور: admin123")
print(f"\n  Supabase: https://supabase.com/dashboard/project/ofehrwapsjlfoersvxet")
print("=" * 60)
