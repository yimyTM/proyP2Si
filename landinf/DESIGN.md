---
name: FICCT Academic Interface
colors:
  surface: '#f7f9fb'
  surface-dim: '#d8dadc'
  surface-bright: '#f7f9fb'
  surface-container-lowest: '#ffffff'
  surface-container-low: '#f2f4f6'
  surface-container: '#eceef0'
  surface-container-high: '#e6e8ea'
  surface-container-highest: '#e0e3e5'
  on-surface: '#191c1e'
  on-surface-variant: '#43474f'
  inverse-surface: '#2d3133'
  inverse-on-surface: '#eff1f3'
  outline: '#737780'
  outline-variant: '#c3c6d1'
  surface-tint: '#3a5f94'
  primary: '#001e40'
  on-primary: '#ffffff'
  primary-container: '#003366'
  on-primary-container: '#799dd6'
  inverse-primary: '#a7c8ff'
  secondary: '#505f76'
  on-secondary: '#ffffff'
  secondary-container: '#d0e1fb'
  on-secondary-container: '#54647a'
  tertiary: '#191f25'
  on-tertiary: '#ffffff'
  tertiary-container: '#2e343a'
  on-tertiary-container: '#969ca4'
  error: '#ba1a1a'
  on-error: '#ffffff'
  error-container: '#ffdad6'
  on-error-container: '#93000a'
  primary-fixed: '#d5e3ff'
  primary-fixed-dim: '#a7c8ff'
  on-primary-fixed: '#001b3c'
  on-primary-fixed-variant: '#1f477b'
  secondary-fixed: '#d3e4fe'
  secondary-fixed-dim: '#b7c8e1'
  on-secondary-fixed: '#0b1c30'
  on-secondary-fixed-variant: '#38485d'
  tertiary-fixed: '#dde3eb'
  tertiary-fixed-dim: '#c1c7cf'
  on-tertiary-fixed: '#161c22'
  on-tertiary-fixed-variant: '#41474e'
  background: '#f7f9fb'
  on-background: '#191c1e'
  surface-variant: '#e0e3e5'
typography:
  headline-xl:
    fontFamily: Inter
    fontSize: 40px
    fontWeight: '700'
    lineHeight: 48px
    letterSpacing: -0.02em
  headline-lg:
    fontFamily: Inter
    fontSize: 32px
    fontWeight: '600'
    lineHeight: 40px
    letterSpacing: -0.01em
  headline-md:
    fontFamily: Inter
    fontSize: 24px
    fontWeight: '600'
    lineHeight: 32px
  body-lg:
    fontFamily: Inter
    fontSize: 18px
    fontWeight: '400'
    lineHeight: 28px
  body-md:
    fontFamily: Inter
    fontSize: 16px
    fontWeight: '400'
    lineHeight: 24px
  body-sm:
    fontFamily: Inter
    fontSize: 14px
    fontWeight: '400'
    lineHeight: 20px
  label-md:
    fontFamily: Inter
    fontSize: 12px
    fontWeight: '600'
    lineHeight: 16px
    letterSpacing: 0.05em
rounded:
  sm: 0.125rem
  DEFAULT: 0.25rem
  md: 0.375rem
  lg: 0.5rem
  xl: 0.75rem
  full: 9999px
spacing:
  unit: 4px
  container-max: 1280px
  gutter: 24px
  margin-desktop: 48px
  margin-mobile: 16px
---

## Brand & Style
The brand personality is authoritative, precise, and forward-thinking. Designed for the Faculty of Computer Science and Telecommunications, the UI must balance high-utility with an institutional sense of trust. The design style follows a **Corporate / Modern** approach with subtle **Minimalist** influences—prioritizing information density and clarity without visual clutter.

The target audience consists of students, researchers, and faculty members who require immediate access to complex data. The emotional response should be one of "effortless competence," where the interface feels like a reliable tool rather than a decorative overlay. Visual interest is generated through precise alignment, intentional use of "tech-silver" surfaces, and a strict adherence to a systematic grid.

## Colors
The palette is rooted in a "Deep Academic Blue" (#003366), representing the stability and history of the institution. This is complemented by "Tech-Silver" (#E2E8F0) and "Slate" (#64748B) to provide a modern, telecommunications-inspired technical feel.

- **Primary:** Used for main branding, primary actions, and active navigation states.
- **Secondary/Neutral:** Used for secondary text, icons, and borders.
- **Surface:** A clean white (#FFFFFF) is used for content cards to maximize legibility, while a light cool gray (#F8FAFC) provides background contrast.
- **States:** Error states use a high-visibility crimson to ensure accessibility in critical academic forms (e.g., enrollment errors).

## Typography
This design system utilizes **Inter** exclusively to leverage its exceptional legibility and systematic feel. The type hierarchy is strictly defined to handle dense academic data and complex form structures.

- **Headlines:** Use tighter letter-spacing and heavier weights to establish clear section hierarchy.
- **Body:** Optimized for long-form reading of course descriptions and policy documents.
- **Labels:** Used for table headers, metadata, and overlines. These are frequently set in uppercase with slight tracking to distinguish them from interactive body text.
- **Mobile scaling:** `headline-xl` and `headline-lg` should scale down to 32px and 24px respectively on mobile devices to maintain layout integrity.

## Layout & Spacing
The layout follows a 12-column **Fixed Grid** for desktop (max-width 1280px) to ensure information is contained and readable on high-resolution laboratory monitors. 

- **Spacing Rhythm:** Based on a 4px baseline grid. 
- **Desktop:** 24px gutters provide ample breathing room between complex data widgets.
- **Mobile:** Reflows to a single column with 16px side margins.
- **Information Density:** For administrative views (like grade transcripts), a "compact" spacing mode is used, reducing vertical padding by 50%.

## Elevation & Depth
Depth is conveyed through **Tonal Layers** and **Low-contrast Outlines** rather than heavy shadows. This maintains a clean, "engineering-grade" aesthetic.

- **Level 0 (Background):** Neutral light gray (#F8FAFC).
- **Level 1 (Cards/Content):** White surface with a 1px solid border (#E2E8F0). No shadow.
- **Level 2 (Interactive/Hover):** Subtle ambient shadow (0px 4px 12px rgba(0,0,0,0.05)) to indicate "lift" when interacting with clickable cards or modules.
- **Level 3 (Modals/Popovers):** Defined by a slightly darker border (#CBD5E1) and a medium-diffused shadow to separate the element from the primary interface.

## Shapes
The shape language is **Soft**, utilizing small border radii to keep the interface feeling contemporary while maintaining a professional, slightly rigid structure.

- **Standard (4px):** Applied to buttons, input fields, and small UI components.
- **Large (8px):** Applied to primary content cards and containers.
- **Extra Large (12px):** Reserved for large layout sections or modal containers.
- **Strictness:** Do not use fully rounded (pill) shapes, as they detract from the technical and academic tone of the application.

## Components
Consistent styling of these core components ensures the application remains predictable and high-trust.

- **Primary Buttons:** Solid Deep Blue background with White text. Uses `4px` corner radius. Hover state shifts to a slightly lighter blue; active state uses a subtle inset shadow.
- **Input Fields:** White background with a `1px` Tech-Silver border. On focus, the border transitions to Primary Blue with a `2px` soft outer glow.
- **Error States:** Input fields turn Crimson (#DC2626) with a secondary text label below the field in the same color, using `body-sm`.
- **Chips:** Used for course tags or status indicators (e.g., "Enrolled"). Use a very light tint of the status color with high-contrast text.
- **Lists/Data Tables:** Use subtle horizontal dividers (#E2E8F0) and no vertical borders. Row hover states use the Neutral background color.
- **Cards:** White surfaces, `1px` border, with a `label-md` header to categorize content sections.