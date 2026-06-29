#!/bin/bash
# ============================================================
# 🚀 نشر Elegance Fashion على Vercel + Supabase (مُهيّأ مسبقًا)
# ============================================================
# قاعدة بيانات Supabase مُنشأة وجاهزة! المشروع: elegance-prod (ref: ofehrwapsjlfoersvxet)
# هذا السكريبت ينشر المشروعين على Vercel فقط.
# ============================================================
set -e

GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
BLUE='\033[0;34m'
NC='\033[0m'

print_step()  { echo -e "${BLUE}▶ $1${NC}"; }
print_ok()    { echo -e "${GREEN}✓ $1${NC}"; }
print_warn()  { echo -e "${YELLOW}⚠ $1${NC}"; }
print_err()   { echo -e "${RED}✗ $1${NC}"; }

# ============================================================
# إعداد المسارات والقيم المُسبقة
# ============================================================
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
ESHOP_REPO_DIR="$(cd "$SCRIPT_DIR/.." && pwd)"
ESHOP_FRONTEND_DIR="$ESHOP_REPO_DIR/ecommerce-eshop"
ESHOP_BACKEND_DIR="$ESHOP_REPO_DIR/ecommerce-shop"

# قيم Supabase المُنشأة مسبقًا (مشروع elegance-prod)
SUPABASE_URL="postgresql://postgres.ofehrwapsjlfoersvxet:EleganceShop2026%21Secure@aws-1-eu-central-1.pooler.supabase.com:6543/postgres"
FRONTEND_APP_KEY="base64:BOdLkv68u7lm8ZDC4QyuRUdB7q5qYtyBy/qc934NHrM="
BACKEND_APP_KEY="base64:GpG7ZNM/+Kx7Tq+0KpDhDdw/CohOY9hHJXtwyg9UI04="

echo ""
echo "============================================================"
echo "  🚀 نشر Elegance Fashion على Vercel + Supabase"
echo "============================================================"
echo ""
echo "  🗄️  قاعدة البيانات: elegance-prod (Supabase)"
echo "  📡  Connection: aws-1-eu-central-1.pooler.supabase.com:6543"
echo ""

# ============================================================
# الخطوة 1: التحقق من Vercel CLI + تسجيل الدخول
# ============================================================
print_step "الخطوة 1: التحقق من Vercel CLI..."

if ! command -v vercel &> /dev/null; then
    print_warn "Vercel CLI غير مُثبّت. جارٍ التثبيت..."
    npm install -g vercel
fi

if ! vercel whoami &> /dev/null; then
    print_warn "غير مُسجّل الدخول في Vercel. سيُفتح المتصفح الآن..."
    echo ""
    echo "  📝 اتبع التعليمات في المتصفح:"
    echo "     1. اختر Continue with GitHub (أو Email)"
    echo "     2. أكّد الصلاحيات"
    echo "     3. ارجع لهنا تلقائيًا"
    echo ""
    vercel login
fi

VERCEL_USER=$(vercel whoami 2>/dev/null | tail -1)
print_ok "مُسجّل الدخول كـ: $VERCEL_USER"

# ============================================================
# الخطوة 2: أسماء المشاريع
# ============================================================
echo ""
print_step "الخطوة 2: أسماء المشاريع على Vercel..."
echo ""
echo "  اختر أسماء فريدة (حروف صغيرة + أرقام + شرطة فقط)"
echo "  اضغط Enter لاستخدام الاسم الافتراضي"
echo ""

read -p "  اسم مشروع الواجهة [elegance-store]: " FRONTEND_PROJECT_NAME
FRONTEND_PROJECT_NAME=${FRONTEND_PROJECT_NAME:-elegance-store}

read -p "  اسم مشروع الـ API [elegance-api]: " BACKEND_PROJECT_NAME
BACKEND_PROJECT_NAME=${BACKEND_PROJECT_NAME:-elegance-api}

print_ok "الواجهة: $FRONTEND_PROJECT_NAME"
print_ok "الـ API:  $BACKEND_PROJECT_NAME"

# ============================================================
# الخطوة 3: نشر الـ Backend أولاً
# ============================================================
echo ""
print_step "الخطوة 3: نشر الـ Backend ($BACKEND_PROJECT_NAME)..."

cd "$ESHOP_BACKEND_DIR"
rm -rf .vercel

vercel link --yes --project "$BACKEND_PROJECT_NAME" 2>&1 | tail -3 || true

# مسح أي env موجود
print_step "ضبط متغيرات البيئة للـ Backend..."
vercel env ls 2>/dev/null | awk '{print $1}' | sort -u | while read -r key; do
    [ -n "$key" ] && vercel env rm "$key" production preview development yes 2>/dev/null || true
done

# إضافة متغيرات الـ Backend
add_env() {
    local key="$1" value="$2"
    printf "%s" "$value" | vercel env add "$key" production preview development 2>/dev/null | tail -1 > /dev/null || true
    echo "  ✓ $key"
}

add_env "APP_NAME" "Elegance Fashion API"
add_env "APP_ENV" "production"
add_env "APP_KEY" "$BACKEND_APP_KEY"
add_env "APP_DEBUG" "true"
add_env "APP_LOCALE" "ar"
add_env "APP_FALLBACK_LOCALE" "en"
add_env "DB_CONNECTION" "pgsql"
add_env "DB_URL" "$SUPABASE_URL"
add_env "RUN_MIGRATIONS_ON_BOOT" "true"
add_env "SESSION_DRIVER" "cookie"
add_env "CACHE_STORE" "array"
add_env "QUEUE_CONNECTION" "sync"
add_env "FILESYSTEM_DISK" "local"
add_env "LOG_CHANNEL" "stderr"
add_env "LOG_LEVEL" "warning"
add_env "SANCTUM_STATEFUL_DOMAINS" "*"
add_env "VIEW_COMPILED_PATH" "/tmp/storage/framework/views"
add_env "APP_CONFIG_CACHE" "/tmp/config.php"
add_env "APP_EVENTS_CACHE" "/tmp/events.php"
add_env "APP_ROUTES_CACHE" "/tmp/routes.php"

print_ok "تم ضبط متغيرات الـ Backend"

# النشر (production)
print_step "نشر الـ Backend على Vercel (يستغرق 5-10 دقائق)..."
vercel --prod --yes 2>&1 | tee /tmp/vercel-backend-deploy.log | tail -10

BACKEND_URL=$(grep -oE 'https://[a-z0-9.-]+\.vercel\.app' /tmp/vercel-backend-deploy.log | tail -1)
print_ok "تم نشر الـ Backend: $BACKEND_URL"

# ============================================================
# الخطوة 4: تشغيل /setup على الـ Backend
# ============================================================
echo ""
print_step "الخطوة 4: تشغيل /setup لزرع البيانات (يستغرق 30-60 ثانية)..."

SETUP_URL="$BACKEND_URL/setup"
echo "  فتح: $SETUP_URL"

for i in 1 2 3 4 5 6 7 8; do
    SETUP_RESPONSE=$(curl -s -m 60 "$SETUP_URL" 2>&1)
    if [[ "$SETUP_RESPONSE" == *"success"* ]] || [[ "$SETUP_RESPONSE" == *"true"* ]]; then
        print_ok "تم زرع البيانات بنجاح!"
        echo "$SETUP_RESPONSE" | python3 -m json.tool 2>/dev/null | head -30 || echo "$SETUP_RESPONSE" | head -200
        break
    fi
    print_warn "محاولة $i... إعادة المحاولة بعد 15 ثانية"
    sleep 15
done

# ============================================================
# الخطوة 5: نشر الـ Frontend
# ============================================================
echo ""
print_step "الخطوة 5: نشر الواجهة ($FRONTEND_PROJECT_NAME)..."

cd "$ESHOP_FRONTEND_DIR"
rm -rf .vercel

vercel link --yes --project "$FRONTEND_PROJECT_NAME" 2>&1 | tail -3 || true

# مسح أي env موجود
print_step "ضبط متغيرات البيئة للواجهة..."
vercel env ls 2>/dev/null | awk '{print $1}' | sort -u | while read -r key; do
    [ -n "$key" ] && vercel env rm "$key" production preview development yes 2>/dev/null || true
done

add_env "APP_NAME" "Elegance Fashion Store"
add_env "APP_ENV" "production"
add_env "APP_KEY" "$FRONTEND_APP_KEY"
add_env "APP_DEBUG" "true"
add_env "APP_LOCALE" "ar"
add_env "APP_FALLBACK_LOCALE" "en"
add_env "DB_CONNECTION" "pgsql"
add_env "DB_URL" "$SUPABASE_URL"
add_env "RUN_MIGRATIONS_ON_BOOT" "true"
add_env "API_BASE_URL" "$BACKEND_URL/api/v1"
add_env "API_TIMEOUT" "15"
add_env "API_RETRIES" "2"
add_env "SESSION_DRIVER" "cookie"
add_env "CACHE_STORE" "array"
add_env "QUEUE_CONNECTION" "sync"
add_env "FILESYSTEM_DISK" "local"
add_env "LOG_CHANNEL" "stderr"
add_env "LOG_LEVEL" "warning"
add_env "SANCTUM_STATEFUL_DOMAINS" "*"
add_env "VIEW_COMPILED_PATH" "/tmp/storage/framework/views"
add_env "APP_CONFIG_CACHE" "/tmp/config.php"
add_env "APP_EVENTS_CACHE" "/tmp/events.php"
add_env "APP_ROUTES_CACHE" "/tmp/routes.php"

print_ok "تم ضبط متغيرات الواجهة"

# النشر
print_step "نشر الواجهة على Vercel (يستغرق 5-10 دقائق)..."
vercel --prod --yes 2>&1 | tee /tmp/vercel-frontend-deploy.log | tail -10

FRONTEND_URL=$(grep -oE 'https://[a-z0-9.-]+\.vercel\.app' /tmp/vercel-frontend-deploy.log | tail -1)
print_ok "تم نشر الواجهة: $FRONTEND_URL"

# ============================================================
# الخطوة 6: الملخص النهائي
# ============================================================
echo ""
echo "============================================================"
echo -e "${GREEN}  ✅ تم النشر بنجاح!${NC}"
echo "============================================================"
echo ""
echo "  🛍️  المتجر:    $FRONTEND_URL"
echo "  📡  الـ API:    $BACKEND_URL"
echo "  🗄️  قاعدة البيانات: elegance-prod (Supabase, eu-central-1)"
echo ""
echo "  حساب الأدمن:"
echo "    البريد:      admin@elegance.com"
echo "    كلمة المرور: admin123"
echo ""
echo "  روابط مفيدة:"
echo "    - سجل المايغريشن: $FRONTEND_URL/_debug/migrate"
echo "    - API المنتجات:    $BACKEND_URL/api/v1/products"
echo "    - Supabase DB:    https://supabase.com/dashboard/project/ofehrwapsjlfoersvxet"
echo ""
echo "============================================================"
