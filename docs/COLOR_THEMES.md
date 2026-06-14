# PUP Calauan Voting System Color Themes

Generated: 2026-04-18  
Scope: app/static/*.css, app/static/css/input.css, app/templates/*.html  
Note: Vendor and minified files were excluded from extraction for a clean reusable palette (tailwind-custom.css, fontawesome.min.css).

## 1. Core Brand Tokens (Maroon Identity)

- Brand primary: #800000
- Brand hover/dark: #660000
- Brand dark: #5c0000
- Brand deep: #5a0000
- Brand sidebar border: #4d0000
- Brand deepest gradient end: #4a0000
- Brand accent deep red: #8b0000
- Brand tints: #a52a2a, #a83232, #cd5c5c
- Brand soft backgrounds: #fbeee6, #f9efe9, #f3e2dc, #e8d2c9

Official design variables already present in the project:

~~~css
:root {
  --pup-maroon: #800000;
  --pup-maroon-dark: #5c0000;
  --pup-maroon-deep: #660000;
  --pup-rose: #fbeee6;
}
~~~

## 2. Login Theme (Exact Colors Used)

### 2.1 Main Login Palette

- Page background gradient: linear-gradient(135deg, #fdf2f2 0%, #f5f5f5 100%)
- Illustration gradient: linear-gradient(135deg, #800000 0%, #a52a2a 50%, #cd5c5c 100%)
- Card background: #ffffff
- Primary text/button/link color: #800000
- Primary hover color: #660000
- Back button background: #f3f4f6
- Back button hover background: #e5e7eb
- Input focus ring/border: #800000
- Soft neutrals used with login: #f3f4f6, #e5e7eb, #9ca3af, #6b7280

### 2.2 Login Shadow and Overlay Values

- Card shadow: rgba(0, 0, 0, 0.15)
- Back button shadow: rgba(0, 0, 0, 0.05)
- Illustration drop shadow: rgba(0,0,0,0.2)

## 3. Complementary Theme Families Across the System

### 3.1 Neutrals (Structure, typography, surfaces)

#000, #111827, #1e1e1e, #1e293b, #334155, #374151, #475569, #4b5563, #64748b, #6b7280, #94a3b8, #9ca3af, #b0b0b0, #cbd5e1, #d1d5db, #ddd, #dddddd, #e0e0e0, #e2e8f0, #e5e7eb, #f1f5f9, #f3f4f6, #f8fafc, #f9f9f9, #f9fafb, #fafafa, #fafbfc, #fff, #ffffff

### 3.2 Success (Positive actions and confirmations)

#047857, #059669, #065f46, #0f766e, #10b981, #14532d, #15803d, #166534, #16a34a, #22c55e, #6ee7b7, #86efac, #bbf7d0, #d1fae5, #dcfce7, #f0fdf4

### 3.3 Info (Informational state)

#1d4ed8, #1e3a5f, #1e3a8a, #1e40af, #2563eb, #3b82f6, #4338ca, #60a5fa, #93c5fd, #bfdbfe, #dbeafe, #eef2ff, #eff6ff

### 3.4 Warning and Gold Highlights

#92400e, #9a3412, #a16207, #b45309, #ca8a04, #d97706, #ea580c, #eab308, #f59e0b, #f59e42, #f97316, #fbbf24, #fcd34d, #fde68a, #fef3c7, #fef9c3, #fef9f0, #ffedd5, #fff7ed, #fffbeb

### 3.5 Danger (Errors and destructive states)

#991b1b, #b91c1c, #be123c, #dc2626, #ef4444, #f87171, #fca5a5, #fecaca, #fee2e2, #fef2f2, #fff1f2

### 3.6 Accent (Secondary purple/indigo accents)

#6366f1, #6b21a8, #6d28d9, #7e22ce, #9333ea, #ede9fe, #f3e8ff, #faf5ff

## 4. Reusable Theme Bundles (Copy to Other Projects)

### 4.1 Bundle A: Classic PUP Maroon (Best for login and primary CTAs)

- Background: #fdf2f2
- Surface: #ffffff
- Primary: #800000
- Primary hover: #660000
- Primary deep: #5c0000
- Soft accent surface: #fbeee6
- Text strong: #111827
- Text muted: #6b7280

### 4.2 Bundle B: Maroon + Slate Admin

- Sidebar: #800000
- Sidebar active: #660000
- Sidebar border: #4d0000
- Main background: #f8fafc
- Card background: #ffffff
- Body text: #334155
- Divider: #e2e8f0

### 4.3 Bundle C: Maroon + Gold Winners Highlight

- Primary maroon: #800000
- Gold primary: #fbbf24
- Gold soft: #fde68a
- Gold card background: #fef9f0
- Warm text: #92400e
- Neutral support: #f3f4f6, #e5e7eb

### 4.4 Bundle D: Maroon + Status Colors (Dashboard/alerts)

- Brand: #800000
- Success: #16a34a
- Info: #2563eb
- Warning: #f59e0b
- Danger: #dc2626
- Neutral text: #475569
- Surface: #ffffff

## 5. Gradient Library (Exact Values in Use)

- linear-gradient(135deg, #800000 0%, #4a0000 100%)
- linear-gradient(135deg, #800000 0%, #4d0000 100%)
- linear-gradient(135deg, #800000 0%, #5a0000 100%)
- linear-gradient(135deg, #800000 0%, #a52a2a 50%, #cd5c5c 100%)
- linear-gradient(135deg, #800000, #a52a2a)
- linear-gradient(135deg, #fbbf24, #f59e0b)
- linear-gradient(135deg, #fdf2f2 0%, #f5f5f5 100%)
- linear-gradient(135deg, #fef2f2 0%, #fff5f5 100%)
- linear-gradient(135deg,#065f46,#047857)
- linear-gradient(135deg,#1e3a5f,#1e40af)
- linear-gradient(135deg,#800000,#991b1b)
- linear-gradient(135deg,#92400e,#b45309)
- linear-gradient(135deg,#eff6ff,#eef2ff)
- linear-gradient(135deg,#f8fafc,#f1f5f9)
- linear-gradient(135deg,#fde8e8,#f8d0d0)
- linear-gradient(135deg,#fef3c7,#fde68a)
- linear-gradient(135deg,#fefce8 0%,#fff7ed 50%,#fffbeb 100%)
- linear-gradient(90deg, #800000, #dc2626)
- linear-gradient(90deg, #e5e7eb 25%, #f3f4f6 50%, #e5e7eb 75%)
- linear-gradient(90deg,#22c55e,#16a34a)
- linear-gradient(90deg,#60a5fa,#2563eb)
- linear-gradient(90deg,#6b7280,#9ca3af)
- linear-gradient(90deg,#800000,#a52a2a)
- linear-gradient(90deg,#b45309,#d97706)
- linear-gradient(90deg,#f3f4f6 25%,#fafafa 50%,#f3f4f6 75%)
- linear-gradient(90deg,#f87171,#dc2626)
- linear-gradient(90deg,#fbbf24,#d97706)
- linear-gradient(90deg,#fef9f0 25%,#fff 50%,#fef9f0 75%)
- linear-gradient(90deg,transparent,rgba(255,255,255,.3),transparent)

## 6. Overlay, Glass, and Shadow Alpha Tokens

- rgba(0,0,0,.06)
- rgba(0,0,0,.08)
- rgba(0,0,0,.12)
- rgba(0,0,0,.18)
- rgba(0,0,0,.2)
- rgba(0,0,0,.3)
- rgba(0,0,0,.35)
- rgba(0,0,0,.5)
- rgba(0,0,0,.65)
- rgba(0,0,0,0.05)
- rgba(0,0,0,0.06)
- rgba(0,0,0,0.1)
- rgba(0,0,0,0.15)
- rgba(0,0,0,0.2)
- rgba(0,0,0,0.3)
- rgba(0,0,0,0.5)
- rgba(0,0,0,0.55)
- rgba(0,0,0,0.75)
- rgba(0,0,0,0.8)
- rgba(0,0,0,0.85)
- rgba(102,0,0,0.8)
- rgba(128,0,0,.04)
- rgba(128,0,0,.08)
- rgba(128,0,0,.1)
- rgba(128,0,0,.25)
- rgba(128,0,0,0.2)
- rgba(128,0,0,0.8)
- rgba(146,64,14,.25)
- rgba(148,163,184,0.1)
- rgba(160,0,0,0.8)
- rgba(180,30,30,0.8)
- rgba(245,158,11,.4)
- rgba(251,191,36,0.3)
- rgba(251,191,36,0.4)
- rgba(255,255,255,.12)
- rgba(255,255,255,.15)
- rgba(255,255,255,.2)
- rgba(255,255,255,.3)
- rgba(255,255,255,.35)
- rgba(255,255,255,.6)
- rgba(255,255,255,.65)
- rgba(255,255,255,.7)
- rgba(255,255,255,.8)
- rgba(255,255,255,.85)
- rgba(255,255,255,0.06)
- rgba(255,255,255,0.08)
- rgba(255,255,255,0.1)
- rgba(255,255,255,0.15)
- rgba(255,255,255,0.18)
- rgba(255,255,255,0.2)
- rgba(255,255,255,0.35)
- rgba(255,255,255,0.6)
- rgba(255,255,255,0.603)
- rgba(255,255,255,0.8)
- rgba(255,255,255,0.95)
- rgba(30,64,175,.25)
- rgba(34,197,94,.5)
- rgba(34,197,94,0)
- rgba(6,95,70,.25)
- rgba(74,0,0,0.8)
- rgba(77,0,0,0.95)
- rgba(77,0,0,0.98)

## 7. Complete Hex Inventory (Deduplicated)

~~~text
#000
#047857
#059669
#065f46
#0f766e
#10b981
#111827
#14532d
#15803d
#166534
#16a34a
#1d4ed8
#1e1e1e
#1e293b
#1e3a5f
#1e3a8a
#1e40af
#218838
#22c55e
#2563eb
#28a745
#333
#334155
#374151
#3b82f6
#4338ca
#475569
#4a0000
#4b5563
#4d0000
#5a0000
#5c0000
#60a5fa
#6366f1
#64748b
#660000
#6b21a8
#6b7280
#6d28d9
#6ee7b7
#7e22ce
#800000
#86efac
#8b0000
#92400e
#9333ea
#93c5fd
#94a3b8
#991b1b
#9a3412
#9ca3af
#a16207
#a52a2a
#a83232
#b0b0b0
#b45309
#b91c1c
#bbf7d0
#be123c
#bfdbfe
#ca8a04
#cbd5e1
#ccc
#cd5c5c
#d1d5db
#d1fae5
#d97706
#dbeafe
#dc2626
#dcfce7
#ddd
#dddddd
#e0e0e0
#e2e8f0
#e5d5d5
#e5e7eb
#e8d2c9
#ea580c
#eab308
#ede9fe
#eef2ff
#ef4444
#eff6ff
#f0fdf4
#f1f5f9
#f3e2dc
#f3e8e8
#f3e8ff
#f3f3f3
#f3f4f6
#f59e0b
#f59e42
#f5f5f5
#f87171
#f8d0d0
#f8fafc
#f97316
#f9efe9
#f9f9f9
#f9fafb
#faf5f5
#faf5ff
#fafafa
#fafbfc
#fbbf24
#fbeee6
#fca5a5
#fcd34d
#fde68a
#fde8e8
#fdf2f2
#fdfafa
#fecaca
#fed7aa
#fee2e2
#fef2f2
#fef3c7
#fef9c3
#fef9f0
#fefce8
#ffcc00
#ffedd5
#fff
#fff1f2
#fff5f5
#fff7ed
#fffbeb
#ffffff
~~~

## 8. Quick Starter Theme Variables (Ready to Paste)

~~~css
:root {
  /* Brand maroon system */
  --color-brand-50: #fdf2f2;
  --color-brand-100: #fbeee6;
  --color-brand-200: #f3e2dc;
  --color-brand-300: #e8d2c9;
  --color-brand-500: #800000;
  --color-brand-600: #660000;
  --color-brand-700: #5c0000;
  --color-brand-800: #5a0000;
  --color-brand-900: #4d0000;

  /* Neutral UI */
  --color-bg: #f8fafc;
  --color-surface: #ffffff;
  --color-border: #e5e7eb;
  --color-text: #111827;
  --color-text-muted: #6b7280;

  /* Semantic states */
  --color-success: #16a34a;
  --color-info: #2563eb;
  --color-warning: #f59e0b;
  --color-danger: #dc2626;

  /* Signature gradients */
  --gradient-login-bg: linear-gradient(135deg, #fdf2f2 0%, #f5f5f5 100%);
  --gradient-brand-hero: linear-gradient(135deg, #800000 0%, #a52a2a 50%, #cd5c5c 100%);
  --gradient-brand-cta: linear-gradient(90deg, #800000, #dc2626);
}
~~~
