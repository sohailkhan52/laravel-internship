import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
            ],
            refresh: true,
        }),
    ],
});



import './bootstrap';
import '../css/app.css';


@import 'custom.css';



/* ========================================
   GLOBAL STYLES & VARIABLES
   ======================================== */
:root {
    /* Modern Color Palette */
    --primary-gradient: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
    --secondary-gradient: linear-gradient(135deg, #f59e0b 0%, #f97316 100%);
    --success-gradient: linear-gradient(135deg, #10b981 0%, #34d399 100%);
    --danger-gradient: linear-gradient(135deg, #ef4444 0%, #f87171 100%);
    --dark-gradient: linear-gradient(135deg, #1f2937 0%, #374151 100%);
    
    --primary-color: #6366f1;
    --primary-dark: #4f46e5;
    --secondary-color: #f59e0b;
    --success-color: #10b981;
    --danger-color: #ef4444;
    --warning-color: #fbbf24;
    --info-color: #3b82f6;
    
    --bg-light: #f9fafb;
    --bg-white: #ffffff;
    --text-dark: #111827;
    --text-gray: #6b7280;
    --border-color: #e5e7eb;
    
    --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
    --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1);
    --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1);
    --shadow-xl: 0 20px 25px -5px rgb(0 0 0 / 0.1);
    --shadow-2xl: 0 25px 50px -12px rgb(0 0 0 / 0.25);
}

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    font-family: 'Nunito', 'Segoe UI', sans-serif;
    min-height: 100vh;
}

/* ========================================
   NAVBAR STYLES
   ======================================== */
.navbar {
    background: rgba(255, 255, 255, 0.95) !important;
    backdrop-filter: blur(10px);
    box-shadow: var(--shadow-lg);
    padding: 1rem 0;
    border-bottom: 1px solid rgba(255, 255, 255, 0.2);
}

.navbar-brand {
    font-size: 1.5rem;
    font-weight: 800;
    background: var(--primary-gradient);
    -webkit-background-clip: text;
    background-clip: text;
    color: transparent !important;
    letter-spacing: -0.5px;
}

.navbar-brand:hover {
    background: var(--secondary-gradient);
    -webkit-background-clip: text;
    background-clip: text;
    transform: scale(1.05);
    transition: transform 0.3s ease;
}

.nav-link {
    font-weight: 600;
    color: var(--text-dark) !important;
    position: relative;
    transition: all 0.3s ease;
    margin: 0 0.5rem;
}

.nav-link::after {
    content: '';
    position: absolute;
    width: 0;
    height: 2px;
    bottom: 0;
    left: 50%;
    background: var(--primary-gradient);
    transition: all 0.3s ease;
    transform: translateX(-50%);
}

.nav-link:hover::after {
    width: 80%;
}

.nav-link:hover {
    color: var(--primary-color) !important;
    transform: translateY(-2px);
}

.dropdown-menu {
    border: none;
    box-shadow: var(--shadow-lg);
    border-radius: 1rem;
    padding: 0.5rem;
    margin-top: 0.5rem;
}

.dropdown-item {
    border-radius: 0.5rem;
    transition: all 0.3s ease;
    font-weight: 500;
}

.dropdown-item:hover {
    background: var(--primary-gradient);
    color: white;
    transform: translateX(5px);
}

/* ========================================
   CARD STYLES - Modern Glassmorphism
   ======================================== */
.card {
    border: none;
    border-radius: 1.5rem;
    background: rgba(255, 255, 255, 0.98);
    box-shadow: var(--shadow-xl);
    transition: all 0.3s ease;
    backdrop-filter: blur(10px);
}

.card:hover {
    transform: translateY(-5px);
    box-shadow: var(--shadow-2xl);
}

.card-header {
    background: var(--primary-gradient);
    color: white;
    border: none;
    padding: 1.5rem;
    font-weight: 700;
    font-size: 1.25rem;
}

.card-header h3 {
    margin: 0;
    font-weight: 700;
    letter-spacing: -0.5px;
}

.card-body {
    padding: 2rem;
}

/* ========================================
   FORM STYLES - Modern & Clean
   ======================================== */
.form-control, select.form-control {
    border: 2px solid var(--border-color);
    border-radius: 1rem;
    padding: 0.75rem 1rem;
    transition: all 0.3s ease;
    font-size: 0.95rem;
}

.form-control:focus, select.form-control:focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
    outline: none;
}

.form-label {
    font-weight: 600;
    color: var(--text-dark);
    margin-bottom: 0.5rem;
    font-size: 0.9rem;
}

/* ========================================
   BUTTON STYLES - Beautiful Gradients
   ======================================== */
.btn {
    border: none;
    border-radius: 2rem;
    padding: 0.75rem 1.5rem;
    font-weight: 600;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.btn::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 0;
    height: 0;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.3);
    transform: translate(-50%, -50%);
    transition: width 0.6s, height 0.6s;
}

.btn:hover::before {
    width: 300px;
    height: 300px;
}

.btn-primary {
    background: var(--primary-gradient);
    color: white;
    box-shadow: 0 4px 15px rgba(99, 102, 241, 0.3);
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(99, 102, 241, 0.4);
}

.btn-success {
    background: var(--success-gradient);
    box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);
}

.btn-outline-primary {
    border: 2px solid var(--primary-color);
    background: transparent;
    color: var(--primary-color);
}

.btn-outline-primary:hover {
    background: var(--primary-gradient);
    border-color: transparent;
    transform: translateY(-2px);
}

/* ========================================
   TABLE STYLES - Modern & Elegant
   ======================================== */
.table {
    border-radius: 1rem;
    overflow: hidden;
    box-shadow: var(--shadow-md);
}

.table thead th {
    background: var(--primary-gradient);
    color: white;
    border: none;
    padding: 1rem;
    font-weight: 600;
    text-transform: uppercase;
    font-size: 0.85rem;
    letter-spacing: 0.5px;
}

.table tbody tr {
    transition: all 0.3s ease;
}

.table tbody tr:hover {
    background: linear-gradient(90deg, #f3f4f6 0%, #ffffff 100%);
    transform: scale(1.01);
    box-shadow: var(--shadow-sm);
}

.table td {
    padding: 1rem;
    vertical-align: middle;
    border-bottom: 1px solid var(--border-color);
}

/* ========================================
   ALERT STYLES - Smooth & Colorful
   ======================================== */
.alert {
    border: none;
    border-radius: 1rem;
    padding: 1rem 1.5rem;
    font-weight: 500;
    animation: slideInDown 0.5s ease;
}

.alert-success {
    background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
    color: #065f46;
    border-left: 4px solid var(--success-color);
}

.alert-danger {
    background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
    color: #991b1b;
    border-left: 4px solid var(--danger-color);
}

/* ========================================
   CHAT INTERFACE STYLES
   ======================================== */
.list-group-item {
    border: none;
    border-radius: 1rem !important;
    margin-bottom: 0.5rem;
    transition: all 0.3s ease;
    cursor: pointer;
}

.list-group-item:hover {
    transform: translateX(5px);
    background: linear-gradient(90deg, #f3f4f6 0%, #ffffff 100%);
}

.list-group-item.active {
    background: var(--primary-gradient);
    border: none;
    box-shadow: var(--shadow-md);
}

.message-item {
    animation: fadeInUp 0.3s ease;
}

.bg-primary.text-white .message-item {
    background: rgba(255, 255, 255, 0.1);
}

/* Chat Message Bubbles */
.rounded-3 {
    border-radius: 1.5rem !important;
    animation: messagePop 0.3s ease;
}

/* ========================================
   ANIMATIONS
   ======================================== */
@keyframes slideInDown {
    from {
        opacity: 0;
        transform: translateY(-30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes messagePop {
    0% {
        opacity: 0;
        transform: scale(0.8);
    }
    100% {
        opacity: 1;
        transform: scale(1);
    }
}

@keyframes pulse {
    0%, 100% {
        transform: scale(1);
    }
    50% {
        transform: scale(1.05);
    }
}

/* ========================================
   RESPONSIVE DESIGN
   ======================================== */
@media (max-width: 768px) {
    .card-body {
        padding: 1rem;
    }
    
    .table {
        font-size: 0.85rem;
    }
    
    .btn {
        padding: 0.5rem 1rem;
        font-size: 0.85rem;
    }
    
    .navbar-brand {
        font-size: 1.2rem;
    }
}

/* ========================================
   CUSTOM SCROLLBAR
   ======================================== */
::-webkit-scrollbar {
    width: 8px;
    height: 8px;
}

::-webkit-scrollbar-track {
    background: var(--bg-light);
    border-radius: 10px;
}

::-webkit-scrollbar-thumb {
    background: var(--primary-gradient);
    border-radius: 10px;
}

::-webkit-scrollbar-thumb:hover {
    background: var(--primary-dark);
}

/* ========================================
   BADGE STYLES
   ======================================== */
.badge {
    padding: 0.5rem 0.75rem;
    border-radius: 2rem;
    font-weight: 600;
    animation: pulse 2s infinite;
}

.badge-danger, .bg-danger {
    background: var(--danger-gradient) !important;
}

/* ========================================
   PAGINATION STYLES
   ======================================== */
.pagination {
    margin-top: 2rem;
    justify-content: center;
}

.page-link {
    border: none;
    margin: 0 0.25rem;
    border-radius: 0.5rem;
    color: var(--primary-color);
    font-weight: 500;
    transition: all 0.3s ease;
}

.page-link:hover {
    background: var(--primary-gradient);
    color: white;
    transform: translateY(-2px);
}

.page-item.active .page-link {
    background: var(--primary-gradient);
    border: none;
}

/* ========================================
   MODAL STYLES
   ======================================== */
.modal-content {
    border: none;
    border-radius: 1.5rem;
    box-shadow: var(--shadow-2xl);
}

.modal-header {
    background: var(--primary-gradient);
    color: white;
    border: none;
    border-radius: 1.5rem 1.5rem 0 0;
}

.modal-footer {
    border: none;
}

/* ========================================
   ARABIC & TRANSLATION TEXT STYLES
   ======================================== */
.arabic-text {
    font-size: 1.25rem;
    line-height: 2rem;
    direction: rtl;
    font-weight: 500;
    color: #1e293b;
}

.translation-text {
    font-size: 1rem;
    line-height: 1.75rem;
    color: var(--text-gray);
}

/* ========================================
   LOADING & HOVER EFFECTS
   ======================================== */
.btn:active {
    transform: translateY(0);
}

input:focus, select:focus, textarea:focus {
    outline: none;
}

/* Glass morphism effect for containers */
.container, .container-fluid {
    animation: fadeInUp 0.6s ease;
}

/* Special effect for the translation container */
#translationContainer {
    animation: fadeInUp 0.5s ease;
}

/* Font selector styling */
#simple-olor {
    background: white;
    border: 2px solid var(--border-color);
    border-radius: 2rem;
    cursor: pointer;
}

#simple-olor:hover {
    border-color: var(--primary-color);
    box-shadow: var(--shadow-md);
}

/* ========================================
   DASHBOARD SPECIFIC STYLES
   ======================================== */
h3.mx-auto {
    background: var(--primary-gradient);
    -webkit-background-clip: text;
    background-clip: text;
    color: transparent;
    font-weight: 800;
    font-size: 2rem;
    text-align: center;
    margin-bottom: 2rem;
}

/* ========================================
   TOOLTIP & HELPER CLASSES
   ======================================== */
.shadow-hover {
    transition: all 0.3s ease;
}

.shadow-hover:hover {
    box-shadow: var(--shadow-xl);
    transform: translateY(-5px);
}

.gradient-text {
    background: var(--primary-gradient);
    -webkit-background-clip: text;
    background-clip: text;
    color: transparent;
}