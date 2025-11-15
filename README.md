# TEBBPLUS Medical Platform

## 📋 Project Summary / وصف المشروع

### English

**TEBBPLUS Medical Platform** is a comprehensive, multi-role healthcare management system built with Laravel. The platform serves as an integrated digital ecosystem connecting clinics, doctors, patients, suppliers, and medical professionals across Egypt.

#### Key Features:

- **🏥 Clinic Management**: Complete clinic administration dashboard with patient management, appointment scheduling, medical records, prescriptions, lab orders, inventory management, staff management, and financial tracking.

- **👨‍⚕️ Doctor Profiles**: Public doctor profiles with specialties, experience, education, and services. Profile approval workflow with admin review system.

- **👥 Patient Portal**: Patient registration, appointment booking, medical history access, prescription viewing, and lab results tracking.

- **🛒 Medical Supplies Marketplace**: E-commerce platform for medical equipment and supplies with supplier management, product catalog, shopping cart, and checkout system.

- **🏢 Rental Spaces**: Medical space rental service connecting doctors with clinic owners for flexible practice locations.

- **📚 Training & Development**: Medical courses and training programs with enrollment system.

- **📰 Medical Blog**: Content management system for medical articles and news.

- **💼 Job Board**: Medical job listings and application system.

- **💰 Financial Management**: Salary contracts, payslips, expense tracking, and financial reporting for clinics.

- **📊 Analytics & Reporting**: Comprehensive dashboards with analytics for appointments, revenue, and clinic performance.

#### Technical Stack:
- **Backend**: Laravel 10+ (PHP 8.1+)
- **Frontend**: Blade Templates, Bootstrap, jQuery, DataTables
- **Database**: MySQL with comprehensive migrations and seeders
- **Authentication**: Multi-guard system (Admin, Clinic, Supplier, Patient)
- **Authorization**: Role-based permissions using Spatie Laravel Permission
- **Payment Gateways**: Multiple payment gateway integration
- **Media Management**: Spatie Media Library for file handling
- **Localization**: Arabic and English language support

---

### العربية

**منصة TEBBPLUS الطبية** هي نظام شامل متعدد الأدوار لإدارة الرعاية الصحية مبني على Laravel. تعمل المنصة كنظام رقمي متكامل يربط بين العيادات والأطباء والمرضى والموردين والمهنيين الطبيين في جميع أنحاء مصر.

#### المميزات الرئيسية:

- **🏥 إدارة العيادات**: لوحة تحكم كاملة لإدارة العيادات تشمل إدارة المرضى، جدولة المواعيد، السجلات الطبية، الوصفات الطبية، طلبات المختبرات، إدارة المخزون، إدارة الموظفين، وتتبع المالية.

- **👨‍⚕️ ملفات الأطباء**: ملفات عامة للأطباء مع التخصصات والخبرة والتعليم والخدمات. نظام موافقة على الملفات مع مراجعة من قبل الإدارة.

- **👥 بوابة المرضى**: تسجيل المرضى، حجز المواعيد، الوصول إلى التاريخ الطبي، عرض الوصفات الطبية، وتتبع نتائج المختبرات.

- **🛒 سوق المستلزمات الطبية**: منصة تجارة إلكترونية للمعدات والمستلزمات الطبية مع إدارة الموردين، كتالوج المنتجات، سلة التسوق، ونظام الدفع.

- **🏢 مساحات الإيجار**: خدمة إيجار المساحات الطبية التي تربط الأطباء بأصحاب العيادات لمواقع ممارسة مرنة.

- **📚 التدريب والتطوير**: دورات طبية وبرامج تدريبية مع نظام التسجيل.

- **📰 المدونة الطبية**: نظام إدارة المحتوى للمقالات والأخبار الطبية.

- **💼 لوحة الوظائف**: قوائم الوظائف الطبية ونظام التقديم.

- **💰 الإدارة المالية**: عقود الرواتب، كشوف المرتبات، تتبع المصروفات، والتقارير المالية للعيادات.

- **📊 التحليلات والتقارير**: لوحات تحكم شاملة مع تحليلات للمواعيد والإيرادات وأداء العيادات.

#### التقنيات المستخدمة:
- **الخلفية**: Laravel 10+ (PHP 8.1+)
- **الواجهة الأمامية**: Blade Templates، Bootstrap، jQuery، DataTables
- **قاعدة البيانات**: MySQL مع migrations و seeders شاملة
- **المصادقة**: نظام multi-guard (Admin، Clinic، Supplier، Patient)
- **الصلاحيات**: صلاحيات قائمة على الأدوار باستخدام Spatie Laravel Permission
- **بوابات الدفع**: تكامل بوابات دفع متعددة
- **إدارة الوسائط**: Spatie Media Library للتعامل مع الملفات
- **الترجمة**: دعم اللغة العربية والإنجليزية

---

## 🚀 Getting Started

### Prerequisites
- PHP >= 8.1
- Composer
- MySQL >= 5.7
- Node.js & NPM

### Installation

1. Clone the repository
```bash
git clone <repository-url>
cd medical-platform
```

2. Install dependencies
```bash
composer install
npm install
```

3. Configure environment
```bash
cp .env.example .env
php artisan key:generate
```

4. Run migrations and seeders
```bash
php artisan migrate --seed
```

5. Start development server
```bash
php artisan serve
npm run dev
```

## 📖 Documentation

- [Database Seeders Guide](database/seeders/README.md)
- [Cart & Checkout Guide](guides/CART_CHECKOUT_GUIDE.md)
- [Payment Gateways](guides/payment/)
- [Deployment Guide](DEPLOYMENT.md)

## 🔐 Default Credentials

See [Credentials.md](Credentials.md) for default login credentials.

## 📝 License

This project is proprietary software. All rights reserved.

---

<p align="center">© 2024 TEBBPLUS Medical Platform. All rights reserved.</p>
