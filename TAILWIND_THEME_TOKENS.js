/**
 * PUP Calauan Voting System Tailwind theme tokens.
 *
 * Usage in tailwind.config.js:
 * const { pupThemeTokens } = require('./TAILWIND_THEME_TOKENS');
 *
 * module.exports = {
 *   content: ['all template html files'],
 *   theme: {
 *     extend: {
 *       ...pupThemeTokens,
 *     },
 *   },
 *   plugins: [],
 * };
 */

const pupThemeTokens = {
  colors: {
    brand: {
      50: '#fdf5f3',
      100: '#f9edea',
      200: '#e6d4cd',
      300: '#d9beb5',
      500: '#7a0000',
      600: '#670000',
      700: '#590000',
      800: '#4f0000',
      900: '#450000',
      950: '#3c0000',
    },
    brandAccent: {
      deepRed: '#8b0000',
      tint: '#a52a2a',
      tintStrong: '#a83232',
      tintSoft: '#cd5c5c',
    },
    neutral: {
      50: '#f7f9fb',
      100: '#f2f5f8',
      200: '#e3e9f0',
      300: '#d4dde8',
      400: '#bdc9d8',
      500: '#8698af',
      600: '#5f738c',
      700: '#44566f',
      800: '#2f3f54',
      900: '#1f2c40',
      950: '#111d2f',
    },
    success: {
      50: '#f0fdf4',
      100: '#dcfce7',
      200: '#bbf7d0',
      300: '#86efac',
      500: '#22c55e',
      600: '#16a34a',
      700: '#15803d',
      800: '#166534',
      900: '#14532d',
    },
    info: {
      50: '#eff6ff',
      100: '#dbeafe',
      200: '#bfdbfe',
      300: '#93c5fd',
      400: '#60a5fa',
      500: '#3b82f6',
      600: '#2563eb',
      700: '#1d4ed8',
      800: '#1e40af',
      900: '#1e3a8a',
    },
    warning: {
      50: '#fffbeb',
      100: '#fef3c7',
      200: '#fde68a',
      300: '#fcd34d',
      400: '#fbbf24',
      500: '#f59e0b',
      600: '#d97706',
      700: '#b45309',
      800: '#92400e',
      900: '#9a3412',
    },
    danger: {
      50: '#fef2f2',
      100: '#fee2e2',
      200: '#fecaca',
      300: '#fca5a5',
      400: '#f87171',
      500: '#ef4444',
      600: '#dc2626',
      700: '#b91c1c',
      800: '#991b1b',
      900: '#be123c',
    },
    accent: {
      50: '#faf5ff',
      100: '#f3e8ff',
      500: '#9333ea',
      600: '#7e22ce',
      700: '#6d28d9',
      800: '#6b21a8',
      900: '#4338ca',
    },
    surface: {
      page: '#eceff4',
      card: '#fdfcfb',
      rose: '#f7ece8',
      warm: '#f6f1ee',
      muted: '#eef2f5',
    },
    overlay: {
      black85: 'rgba(0,0,0,0.85)',
      black75: 'rgba(0,0,0,0.75)',
      black65: 'rgba(0,0,0,0.65)',
      white18: 'rgba(255,255,255,0.18)',
      white35: 'rgba(255,255,255,0.35)',
      brand08: 'rgba(128,0,0,.08)',
      brand25: 'rgba(128,0,0,.25)',
    },
  },

  backgroundImage: {
    'login-bg': 'linear-gradient(135deg, #fdf2f2 0%, #f5f5f5 100%)',
    'login-hero': 'linear-gradient(135deg, #800000 0%, #a52a2a 50%, #cd5c5c 100%)',
    'brand-hero': 'linear-gradient(135deg, #800000 0%, #5a0000 100%)',
    'brand-cta': 'linear-gradient(90deg, #800000, #dc2626)',
    'status-success': 'linear-gradient(135deg,#065f46,#047857)',
    'status-info': 'linear-gradient(135deg,#1e3a5f,#1e40af)',
    'status-danger': 'linear-gradient(135deg,#800000,#991b1b)',
    'status-warning': 'linear-gradient(135deg,#92400e,#b45309)',
    'winner-gold': 'linear-gradient(135deg,#fef3c7,#fde68a)',
    'panel-neutral': 'linear-gradient(135deg,#f8fafc,#f1f5f9)',
    'panel-soft-blue': 'linear-gradient(135deg,#eff6ff,#eef2ff)',
    'skeleton-neutral': 'linear-gradient(90deg, #e5e7eb 25%, #f3f4f6 50%, #e5e7eb 75%)',
    'skeleton-warm': 'linear-gradient(90deg,#fef9f0 25%,#fff 50%,#fef9f0 75%)',
    'shine-overlay': 'linear-gradient(90deg,transparent,rgba(255,255,255,.3),transparent)',
  },

  boxShadow: {
    card: '0 20px 40px -20px rgba(15,23,42,.24)',
    soft: '0 4px 10px rgba(15,23,42,.08)',
    brand: '0 10px 18px rgba(122,0,0,.22)',
    success: '0 8px 30px rgba(6,95,70,.25)',
    info: '0 8px 30px rgba(30,64,175,.25)',
    warning: '0 8px 30px rgba(146,64,14,.25)',
  },

  ringColor: {
    brand: '#800000',
    brandSoft: 'rgba(128,0,0,0.2)',
    white: '#ffffff',
  },

  borderColor: {
    brand: '#800000',
    brandDark: '#660000',
    brandBorder: '#4d0000',
    soft: '#d6deea',
  },
};

const pupThemePreset = {
  theme: {
    extend: {
      ...pupThemeTokens,
    },
  },
};

module.exports = {
  pupThemeTokens,
  pupThemePreset,
};
