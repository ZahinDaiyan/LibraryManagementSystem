<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$APP_I18N = array(
    'en' => array(
        'language.english' => 'English',
        'language.bengali' => 'বাংলা',
        'language.switch' => 'Language',
        'auth.login.title' => 'Login',
        'auth.back_home' => 'Back to home',
        'auth.email' => 'Email:',
        'auth.password' => 'Password:',
        'auth.submit_login' => 'Login',
        'auth.no_account' => 'Don\'t have an account?',
        'auth.create_account' => 'Create New Account',
        'auth.register.title' => 'Member Registration',
        'auth.name' => 'Name:',
        'auth.phone' => 'Phone:',
        'auth.confirm_password' => 'Confirm Password:',
        'auth.branch' => 'Primary Branch ID:',
        'auth.submit_register' => 'Register',
        'auth.have_account' => 'Already have an account?',
        'auth.login_link' => 'Login',
        'home.brand_title' => 'Library Management System',
        'home.brand_tagline' => 'Discover, borrow, and stay informed',
        'home.nav.featured' => 'Featured Books',
        'home.nav.announcements' => 'Announcements',
        'home.nav.about' => 'About',
        'home.nav.contact' => 'Contact',
        'home.login' => 'Login',
        'home.hero.badge' => 'Welcome to the library',
        'home.hero.title' => 'A calmer way to explore your next read.',
        'home.hero.description' => 'Browse the catalog, check live availability, read announcements, and log in only when you are ready to borrow. The library is open to visitors and members alike, with a clean experience across mobile and desktop.',
        'home.hero.browse' => 'Browse Books',
        'home.hero.news' => 'See News',
        'home.stat.featured' => 'Featured titles curated from the collection',
        'home.stat.announcements' => 'Latest announcements and library updates',
        'home.stat.access' => 'Catalog access for browsing and discovery',
        'home.panel.caption' => 'Open shelves, calm spaces, and modern services.',
        'home.panel.browse.title' => 'Browse books',
        'home.panel.browse.body' => 'Search by title, author, ISBN, genre, or branch availability.',
        'home.panel.borrow.title' => 'Borrow safely',
        'home.panel.borrow.body' => 'Visitors are sent to login before borrowing, with a clear message.',
        'home.panel.news.title' => 'Stay updated',
        'home.panel.news.body' => 'See branch and system announcements without signing in.',
        'home.panel.roles.title' => 'Role-aware access',
        'home.panel.roles.body' => 'Members and librarians land in the right dashboard after login.',
        'home.panel.language.title' => 'Bangla & English',
        'home.panel.language.body' => 'Switch languages anytime so the site feels natural for every visitor.',
        'home.featured.title' => 'Featured Books',
        'home.featured.body' => 'Highlighted titles with live availability so visitors can browse before they sign in.',
        'home.featured.catalog' => 'View full catalog',
        'home.featured.empty' => 'No books are available yet.',
        'home.featured.details' => 'View Details',
        'home.announcements.title' => 'Announcements & News',
        'home.announcements.body' => 'Quick updates from the library, including branch notices and system-wide news.',
        'home.announcements.empty' => 'No announcements published yet.',
        'home.about.title' => 'About the library',
        'home.about.body1' => 'Our library system is designed for visitors who want to explore the catalog and for members who want a smooth path into borrowing, reviews, and their personal dashboard.',
        'home.about.body2' => 'The current setup keeps the existing database structure, role-based access, and branch inventory data intact while improving the first impression and the public browsing flow.',
        'home.contact.title' => 'Contact & footer',
        'home.contact.desk' => 'Library Desk:',
        'home.contact.phone' => 'Phone:',
        'home.contact.hours' => 'Hours:',
        'home.contact.body' => 'Need to borrow a book? Open a title, check branch availability, and log in when you are ready.',
        'home.footer.line1' => 'Library Management System',
        'home.footer.line2' => 'Browse books, check availability, and stay informed before you sign in.',
        'member.dashboard.title' => 'Member Dashboard',
        'member.dashboard.welcome' => 'Welcome, {name}',
        'member.dashboard.notifications' => 'Notifications',
        'member.dashboard.features' => 'Features',
        'member.dashboard.announcements' => 'Library Announcements',
        'member.dashboard.none' => 'No announcements.',
        'member.dashboard.logout' => 'Logout',
        'member.feature.browse' => 'Browse Books',
        'member.feature.loans' => 'Active Loans',
        'member.feature.history' => 'Borrow History',
        'member.feature.reservations' => 'My Reservations',
        'member.feature.reading' => 'Reading List',
        'member.feature.profile' => 'My Profile',
        'member.feature.fines' => 'My Fines',
        'member.feature.support' => 'Support & Complaints',
        'member.action.read' => 'Mark as Read',
        'member.book.title' => 'Book Catalog',
        'member.book.back_dashboard' => 'Back to Dashboard',
        'member.book.back_home' => 'Back to Home',
        'member.profile.title' => 'My Profile',
        'member.profile.back_dashboard' => 'Back to Dashboard',
        'member.profile.personal' => 'Personal Information',
        'member.profile.picture' => 'Profile Picture:',
        'member.profile.name' => 'Name:',
        'member.profile.email' => 'Email:',
        'member.profile.phone' => 'Phone:',
        'member.profile.branch' => 'Primary Branch:',
        'member.profile.status' => 'Membership Status:',
        'member.profile.active' => 'Active',
        'member.profile.inactive' => 'Inactive',
        'member.profile.update' => 'Update Information',
        'member.profile.change_password' => 'Change Password',
        'member.profile.current_password' => 'Current Password:',
        'member.profile.new_password' => 'New Password:',
        'member.profile.confirm_password' => 'Confirm New Password:',
        'member.profile.save_password' => 'Change Password'
    ),
    'bn' => array(
        'language.english' => 'English',
        'language.bengali' => 'বাংলা',
        'language.switch' => 'ভাষা',
        'auth.login.title' => 'লগইন',
        'auth.back_home' => 'হোমে ফিরে যান',
        'auth.email' => 'ইমেইল:',
        'auth.password' => 'পাসওয়ার্ড:',
        'auth.submit_login' => 'লগইন',
        'auth.no_account' => 'অ্যাকাউন্ট নেই?',
        'auth.create_account' => 'নতুন অ্যাকাউন্ট তৈরি করুন',
        'auth.register.title' => 'সদস্য নিবন্ধন',
        'auth.name' => 'নাম:',
        'auth.phone' => 'ফোন:',
        'auth.confirm_password' => 'পাসওয়ার্ড নিশ্চিত করুন:',
        'auth.branch' => 'প্রাথমিক শাখা আইডি:',
        'auth.submit_register' => 'নিবন্ধন',
        'auth.have_account' => 'ইতিমধ্যে অ্যাকাউন্ট আছে?',
        'auth.login_link' => 'লগইন',
        'home.brand_title' => 'লাইব্রেরি ম্যানেজমেন্ট সিস্টেম',
        'home.brand_tagline' => 'খুঁজুন, ধার করুন, এবং আপডেট থাকুন',
        'home.nav.featured' => 'বাছাইকৃত বই',
        'home.nav.announcements' => 'ঘোষণা',
        'home.nav.about' => 'পরিচিতি',
        'home.nav.contact' => 'যোগাযোগ',
        'home.login' => 'লগইন',
        'home.hero.badge' => 'লাইব্রেরিতে স্বাগতম',
        'home.hero.title' => 'আপনার পরের বই খুঁজে দেখার আরও শান্ত উপায়।',
        'home.hero.description' => 'ক্যাটালগ দেখুন, লাইভ অ্যাভেইলেবিলিটি যাচাই করুন, ঘোষণা পড়ুন, আর ধার নিতে প্রস্তুত হলে তবেই লগইন করুন। ভিজিটর এবং সদস্য সবার জন্যই মোবাইল ও ডেস্কটপে পরিষ্কার অভিজ্ঞতা রাখা হয়েছে।',
        'home.hero.browse' => 'বই দেখুন',
        'home.hero.news' => 'খবর দেখুন',
        'home.stat.featured' => 'সংগ্রহ থেকে বাছাই করা বই',
        'home.stat.announcements' => 'সর্বশেষ ঘোষণা ও লাইব্রেরির আপডেট',
        'home.stat.access' => 'দেখা ও খুঁজে পাওয়ার জন্য ক্যাটালগ অ্যাক্সেস',
        'home.panel.caption' => 'খোলা তাক, শান্ত পরিবেশ, আর আধুনিক সেবা।',
        'home.panel.browse.title' => 'বই খুঁজুন',
        'home.panel.browse.body' => 'শিরোনাম, লেখক, ISBN, জেনার, বা শাখা অনুযায়ী অনুসন্ধান করুন।',
        'home.panel.borrow.title' => 'নিরাপদে ধার নিন',
        'home.panel.borrow.body' => 'ধার নেওয়ার আগে ভিজিটরদের লগইনে পাঠানো হয়, স্পষ্ট নির্দেশনার সাথে।',
        'home.panel.news.title' => 'আপডেট থাকুন',
        'home.panel.news.body' => 'লগইন ছাড়াই শাখা ও সিস্টেমের ঘোষণা দেখুন।',
        'home.panel.roles.title' => 'রোলভিত্তিক অ্যাক্সেস',
        'home.panel.roles.body' => 'লগইনের পর সদস্য ও লাইব্রেরিয়ান সঠিক ড্যাশবোর্ডে পৌঁছে যান।',
        'home.panel.language.title' => 'বাংলা ও ইংরেজি',
        'home.panel.language.body' => 'যেকোনো সময় ভাষা বদলান, যাতে সবার জন্য সাইটটি স্বাভাবিক লাগে।',
        'home.featured.title' => 'বাছাইকৃত বই',
        'home.featured.body' => 'লাইভ অ্যাভেইলেবিলিটি সহ হাইলাইটেড বই, যাতে ভিজিটররা লগইনের আগে ব্রাউজ করতে পারেন।',
        'home.featured.catalog' => 'পুরো ক্যাটালগ দেখুন',
        'home.featured.empty' => 'এখনও কোনো বই যোগ করা হয়নি।',
        'home.featured.details' => 'বিস্তারিত দেখুন',
        'home.announcements.title' => 'ঘোষণা ও খবর',
        'home.announcements.body' => 'লাইব্রেরির দ্রুত আপডেট, শাখা-নোটিশ এবং সিস্টেম-সংক্রান্ত খবর।',
        'home.announcements.empty' => 'এখনও কোনো ঘোষণা প্রকাশ করা হয়নি।',
        'home.about.title' => 'লাইব্রেরি সম্পর্কে',
        'home.about.body1' => 'এই সিস্টেমটি এমন ভিজিটরদের জন্য তৈরি, যারা ক্যাটালগ দেখতে চান, আর এমন সদস্যদের জন্যও, যারা ধার নেওয়া, রিভিউ, এবং ব্যক্তিগত ড্যাশবোর্ডে সহজ অভিজ্ঞতা চান।',
        'home.about.body2' => 'বর্তমান সেটআপে আগের ডাটাবেস কাঠামো, রোলভিত্তিক অ্যাক্সেস, এবং শাখাভিত্তিক ইনভেন্টরি ডেটা অপরিবর্তিত রেখে প্রথম দেখাতেই আরও পরিপাটি অভিজ্ঞতা দেওয়া হয়েছে।',
        'home.contact.title' => 'যোগাযোগ ও ফুটার',
        'home.contact.desk' => 'লাইব্রেরি ডেস্ক:',
        'home.contact.phone' => 'ফোন:',
        'home.contact.hours' => 'সময়:',
        'home.contact.body' => 'কোনো বই ধার নিতে চান? বই খুলুন, শাখার অ্যাভেইলেবিলিটি দেখুন, আর প্রস্তুত হলে লগইন করুন।',
        'home.footer.line1' => 'লাইব্রেরি ম্যানেজমেন্ট সিস্টেম',
        'home.footer.line2' => 'বই দেখুন, অ্যাভেইলেবিলিটি যাচাই করুন, আর সাইন ইন করার আগে আপডেট থাকুন।',
        'member.dashboard.title' => 'সদস্য ড্যাশবোর্ড',
        'member.dashboard.welcome' => 'স্বাগতম, {name}',
        'member.dashboard.notifications' => 'নোটিফিকেশন',
        'member.dashboard.features' => 'ফিচারসমূহ',
        'member.dashboard.announcements' => 'লাইব্রেরি ঘোষণা',
        'member.dashboard.none' => 'কোনো ঘোষণা নেই।',
        'member.dashboard.logout' => 'লগআউট',
        'member.feature.browse' => 'বই দেখুন',
        'member.feature.loans' => 'চলমান ধার',
        'member.feature.history' => 'ধারের ইতিহাস',
        'member.feature.reservations' => 'আমার রিজার্ভেশন',
        'member.feature.reading' => 'রিডিং লিস্ট',
        'member.feature.profile' => 'আমার প্রোফাইল',
        'member.feature.fines' => 'আমার জরিমানা',
        'member.feature.support' => 'সাপোর্ট ও অভিযোগ',
        'member.action.read' => 'পড়া হয়েছে চিহ্নিত করুন',
        'member.book.title' => 'বইয়ের ক্যাটালগ',
        'member.book.back_dashboard' => 'ড্যাশবোর্ডে ফিরে যান',
        'member.book.back_home' => 'হোমে ফিরে যান',
        'member.profile.title' => 'আমার প্রোফাইল',
        'member.profile.back_dashboard' => 'ড্যাশবোর্ডে ফিরে যান',
        'member.profile.personal' => 'ব্যক্তিগত তথ্য',
        'member.profile.picture' => 'প্রোফাইল ছবি:',
        'member.profile.name' => 'নাম:',
        'member.profile.email' => 'ইমেইল:',
        'member.profile.phone' => 'ফোন:',
        'member.profile.branch' => 'প্রাথমিক শাখা:',
        'member.profile.status' => 'সদস্যতার অবস্থা:',
        'member.profile.active' => 'সক্রিয়',
        'member.profile.inactive' => 'নিষ্ক্রিয়',
        'member.profile.update' => 'তথ্য আপডেট করুন',
        'member.profile.change_password' => 'পাসওয়ার্ড পরিবর্তন',
        'member.profile.current_password' => 'বর্তমান পাসওয়ার্ড:',
        'member.profile.new_password' => 'নতুন পাসওয়ার্ড:',
        'member.profile.confirm_password' => 'নতুন পাসওয়ার্ড নিশ্চিত করুন:',
        'member.profile.save_password' => 'পাসওয়ার্ড পরিবর্তন'
    )
);

function app_supported_languages()
{
    return array('en', 'bn');
}

function app_bootstrap_language()
{
    $supported = app_supported_languages();

    if (isset($_GET['lang'])) {
        $requested = strtolower(trim((string)$_GET['lang']));
        if (in_array($requested, $supported, true)) {
            $_SESSION['lang'] = $requested;
        }
    }

    if (!isset($_SESSION['lang']) || !in_array($_SESSION['lang'], $supported, true)) {
        $_SESSION['lang'] = 'en';
    }

    return $_SESSION['lang'];
}

function app_current_language()
{
    return app_bootstrap_language();
}

function app_translate($key, $replacements = array())
{
    global $APP_I18N;

    $language = app_current_language();
    $text = isset($APP_I18N[$language][$key]) ? $APP_I18N[$language][$key] : (isset($APP_I18N['en'][$key]) ? $APP_I18N['en'][$key] : $key);

    foreach ($replacements as $search => $replace) {
        $text = str_replace('{' . $search . '}', $replace, $text);
    }

    return $text;
}

function app_language_url($path, $language = null)
{
    $language = $language ?: app_current_language();
    $separator = strpos($path, '?') !== false ? '&' : '?';

    return htmlspecialchars($path . $separator . 'lang=' . rawurlencode($language), ENT_QUOTES, 'UTF-8');
}
