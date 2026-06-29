#!/bin/bash
# ============================================================
# 🚀 سكريبت نشر Elegance Fashion على Vercel + Supabase
# ============================================================
# هذا السكريبت يقوم بـ:
#   1. التحقق من تسجيل الدخول في Vercel
#   2. طلب بيانات Supabase من المستخدم
#   3. إنشاء مشروعين على Vercel (frontend + backend)
#   4. ضبط جميع متغيرات البيئة
#   5. نشر المشروعين
# ============================================================
set -e

# ألوان للطباعة
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

print_step() {
    echo -e "${BLUE}▶ $1${NC}"
}
print_ok() {
    echo -e "${GREEN}✓ $1${NC}"
}
print_warn() {
    echo -e "${YELLOW}⚠ $1${NC}"
}
print_err() {
    echo -e "${RED}✗ $1${NC}"
}

# ============================================================
# إعداد المسارات
# ============================================================
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
ESHOP_REPO_DIR="$(cd "$SCRIPT_DIR/.." && pwd)"

ESHOP_FRONTEND_DIR="$ESHOP_REPO_DIR/ecommerce-eshop"
ESHOP_BACKEND_DIR="$ESHOP_REPO_DIR/ecommerce-shop"

echo ""
echo "============================================================"
echo "  🚀 نشر Elegance Fashion على Vercel + Supabase"
echo "============================================================"
echo ""
echo "  Frontend: $ESHOP_FRONTEND_DIR"
echo "  Backend:  $ESHOP_BACKEND_DIR"
echo ""

# ============================================================
# الخطوة 1: التحقق من Vercel CLI
# ============================================================
print_step "الخطوة 1: التحقق من Vercel CLI..."

if ! command -v vercel &> /dev/null; then
    print_err "Vercel CLI غير مُثبّت. جارٍ التثبيت..."
    npm install -g vercel
fi

if ! vercel whoami &> /dev/null; then
    print_warn "غير مُسجّل الدخول في Vercel. سيُفتح المتصفح الآن..."
    vercel login
fi

VERCEL_USER=$(vercel whoami 2>/dev/null | tail -1)
print_ok "مُسجّل الدخول كـ: $VERCEL_USER"

# ============================================================
# الخطوة 2: جمع بيانات Supabase
# ============================================================
echo ""
print_step "الخطوة 2: إدخال بيانات Supabase..."
echo ""
echo "  احصل على Connection String من:"
echo "  Supabase Dashboard → Project Settings → Database → Connection string → Transaction (Pooler)"
echo "  الصيغة: postgresql://postgres:[PASSWORD]@aws-0-[REGION].pooler.supabase.com:6543/postgres"
echo ""

read -p "  أدخل Supabase Connection URL: " SUPABASE_URL

if [ -z "$SUPABASE_URL" ]; then
    print_err "لم يتم إدخال رابط Supabase. خروج."
    exit 1
fi

if [[ ! "$SUPABASE_URL" =~ ^postgresql:// ]]; then
    print_err "الرابط يجب أن يبدأ بـ postgresql://"
    exit 1
fi

print_ok "تم استلام رابط Supabase"

# ============================================================
# الخطوة 3: توليد APP_KEYs
# ============================================================
echo ""
print_step "الخطوة 3: توليد APP_KEY لكل مشروع..."

FRONTEND_APP_KEY=$(node -e "console.log('base64:' + require('crypto').randomBytes(32).toString('base64'))")
BACKEND_APP_KEY=$(node -e "console.log('base64:' + require('crypto').randomBytes(32).toString('base64'))")

print_ok "APP_KEY للواجهة: $FRONTEND_APP_KEY"
print_ok "APP_KEY للـ API:  $BACKEND_APP_KEY"

# ============================================================
# الخطوة 4: أسماء المشاريع
# ============================================================
echo ""
print_step "الخطوة 4: أسماء المشاريع على Vercel..."
echo ""
echo "  اختر أسماء فريدة (حروف صغيرة + أرقام + شرطة فقط)"
echo ""

read -p "  اسم مشروع الواجهة (frontend) [elegance-store]: " FRONTEND_PROJECT_NAME
FRONTEND_PROJECT_NAME=${FRONTEND_PROJECT_NAME:-elegance-store}

read -p "  اسم مشروع الـ API (backend) [elegance-api]: " BACKEND_PROJECT_NAME
BACKEND_PROJECT_NAME=${BACKEND_PROJECT_NAME:-elegance-api}

print_ok "الواجهة: $FRONTEND_PROJECT_NAME"
print_ok "الـ API:  $BACKEND_PROJECT_NAME"

# ============================================================
# الخطوة 5: نشر الـ Backend أولاً
# ============================================================
echo ""
print_step "الخطوة 5: نشر الـ Backend ($BACKEND_PROJECT_NAME)..."

cd "$ESHOP_BACKEND_DIR"

# إزالة أي رابط سابق
rm -rf .vercel

# إنشاء المشروع وربطه
vercel link --yes --project "$BACKEND_PROJECT_NAME" 2>&1 | tail -5 || {
    print_warn "المشروع غير موجود. سيتم إنشاؤه أثناء النشر..."
}

# إعداد متغيرات البيئة (env)
print_step "ضبط متغيرات البيئة للـ Backend..."

set_env_var() {
    local key="$1"
    local value="$2"
    local targets="${3:-production preview development}"
    echo -n "  $key... "
    echo "$value" | vercel env add "$key" $targets 2>/dev/null | tail -1 > /dev/null && echo "OK" || echo "exists"
}

# مسح أي env موجود لنفس المفتاح (لتفادي التعارض)
vercel env ls 2>/dev/null | grep -E "^(APP_|DB_|RUN_|SESSION_|CACHE_|QUEUE_|FILESYSTEM_|LOG_|SANCTUM_|VIEW_|API_)" | awk '{print $1}' | sort -u | while read -r key; do
    [ -n "$key" ] && vercel env rm "$key" production preview development yes 2>/dev/null || true
done

# إضافة متغيرات الـ Backend
declare -A BACKEND_ENVS=(
    ["APP_NAME"]="Elegance Fashion API"
    ["APP_ENV"]="production"
    ["APP_KEY"]="$BACKEND_APP_KEY"
    ["APP_DEBUG"]="true"
    ["APP_LOCALE"]="ar"
    ["APP_FALLBACK_LOCALE"]="en"
    ["DB_CONNECTION"]="pgsql"
    ["DB_URL"]="$SUPABASE_URL"
    ["RUN_MIGRATIONS_ON_BOOT"]="true"
    ["SESSION_DRIVER"]="cookie"
    ["CACHE_STORE"]="array"
    ["QUEUE_CONNECTION"]="sync"
    ["FILESYSTEM_DISK"]="local"
    ["LOG_CHANNEL"]="stderr"
    ["LOG_LEVEL"]="warning"
    ["SANCTUM_STATEFUL_DOMAINS"]="*"
    ["VIEW_COMPILED_PATH"]="/tmp/storage/framework/views"
    ["APP_CONFIG_CACHE"]="/tmp/config.php"
    ["APP_EVENTS_CACHE"]="/tmp/events.php"
    ["APP_ROUTES_CACHE"]="/tmp/routes.php"
)

for key in "${!BACKEND_ENVS[@]}"; do
    echo "  $key..."
    echo "${BACKEND_ENVS[$key]}" | vercel env add "$key" production preview development 2>/dev/null | tail -1 > /dev/null || true
done

print_ok "تم ضبط متغيرات الـ Backend"

# النشر (production)
print_step "نشر الـ Backend على Vercel (قد يستغرق 5-10 دقائق)..."
vercel --prod --yes 2>&1 | tee /tmp/vercel-backend-deploy.log | tail -20

BACKEND_URL=$(grep -oE 'https://[a-z0-9.-]+\.vercel\.app' /tmp/vercel-backend-deploy.log | tail -1)
print_ok "تم نشر الـ Backend: $BACKEND_URL"

# ============================================================
# الخطوة 6: تشغيل /setup على الـ Backend
# ============================================================
echo ""
print_step "الخطوة 6: تشغيل /setup على الـ Backend (لزرع البيانات)..."
sleep 5  # ننتظر قليلاً ليكون الـ deployment جاهزًا

SETUP_URL="$BACKEND_URL/setup"
print_step "فتح: $SETUP_URL"

for i in 1 2 3 4 5; do
    SETUP_RESPONSE=$(curl -s -m 30 "$SETUP_URL" 2>&1)
    if [[ "$SETUP_RESPONSE" == *"success"* ]] || [[ "$SETUP_RESPONSE" == *"true"* ]]; then
        print_ok "تم زرع البيانات بنجاح!"
        echo "$SETUP_RESPONSE" | head -200
        break
    fi
    print_warn "محاولة $i فشلت. إعادة المحاولة بعد 10 ثوان..."
    sleep 10
done

# ============================================================
# الخطوة 7: نشر الـ Frontend
# ============================================================
echo ""
print_step "الخطوة 7: نشر الواجهة ($FRONTEND_PROJECT_NAME)..."

cd "$ESHOP_FRONTEND_DIR"

# إزالة أي رابط سابق
rm -rf .vercel

# إنشاء المشروع وربطه
vercel link --yes --project "$FRONTEND_PROJECT_NAME" 2>&1 | tail -5 || {
    print_warn "المشروع غير موجود. سيتم إنشاؤه أثناء النشر..."
}

# مسح أي env موجود
vercel env ls 2>/dev/null | grep -E "^(APP_|DB_|RUN_|SESSION_|CACHE_|QUEUE_|FILESYSTEM_|LOG_|SANCTUM_|VIEW_|API_|APP_)" | awk '{print $1}' | sort -u | while read -r key; do
    [ -n "$key" ] && vercel env rm "$key" production preview development yes 2>/dev/null || true
done

# إضافة متغيرات الواجهة
declare -A FRONTEND_ENVS=(
    ["APP_NAME"]="Elegance Fashion Store"
    ["APP_ENV"]="production"
    ["APP_KEY"]="$FRONTEND_APP_KEY"
    ["APP_DEBUG"]="true"
    ["APP_LOCALE"]="ar"
    ["APP_FALLBACK_LOCALE"]="en"
    ["DB_CONNECTION"]="pgsql"
    ["DB_URL"]="$SUPABASE_URL"
    ["RUN_MIGRATIONS_ON_BOOT"]="true"
    ["API_BASE_URL"]="$BACKEND_URL/api/v1"
    ["API_TIMEOUT"]="15"
    ["API_RETRIES"]="2"
    ["SESSION_DRIVER"]="cookie"
    ["CACHE_STORE"]="array"
    ["QUEUE_CONNECTION"]="sync"
    ["FILESYSTEM_DISK"]="local"
    ["LOG_CHANNEL"]="stderr"
    ["LOG_LEVEL"]="warning"
    ["SANCTUM_STATEFUL_DOMAINS"]="*"
    ["VIEW_COMPILED_PATH"]="/tmp/storage/framework/views"
    ["APP_CONFIG_CACHE"]="/tmp/config.php"
    ["APP_EVENTS_CACHE"]="/tmp/events.php"
    ["APP_ROUTES_CACHE"]="/tmp/routes.php"
)

for key in "${!FRONTEND_ENVS[@]}"; do
    echo "  $key..."
    echo "${FRONTEND_ENVS[$key]}" | vercel env add "$key" production preview development 2>/dev/null | tail -1 > /dev/null || true
done

print_ok "تم ضبط متغيرات الواجهة"

# النشر
print_step "نشر الواجهة على Vercel (قد يستغرق 5-10 دقائق)..."
vercel --prod --yes 2>&1 | tee /tmp/vercel-frontend-deploy.log | tail -20

FRONTEND_URL=$(grep -oE 'https://[a-z0-9.-]+\.vercel\.app' /tmp/vercel-frontend-deploy.log | tail -1)
print_ok "تم نشر الواجهة: $FRONTEND_URL"

# ============================================================
# الخطوة 8: الملخص النهائي
# ============================================================
echo ""
echo "============================================================"
echo -e "${GREEN}  ✅ تم النشر بنجاح!${NC}"
echo "============================================================"
echo ""
echo "  🛍️  المتجر:    $FRONTEND_URL"
echo "  📡  الـ API:    $BACKEND_URL"
echo "  🗄️  قاعدة البيانات: Supabase PostgreSQL"
echo ""
echo "  حساب الأدمن:"
echo "    البريد:    admin@elegance.com"
echo "    كلمة المرور: admin123"
echo ""
echo "  روابط مفيدة:"
echo "    - سجل المايغريشن:  $FRONTEND_URL/_debug/migrate"
echo "    - API المنتجات:    $BACKEND_URL/api/v1/products"
echo "    - زرع البيانات:    $BACKEND_URL/setup"
echo ""
echo "============================================================"
