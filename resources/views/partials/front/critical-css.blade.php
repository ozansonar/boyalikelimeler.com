<style>
/* Critical Font-face: only Inter 400 + Playfair Display 700 (preloaded) */
@font-face{font-family:'Inter';font-style:normal;font-weight:400;font-display:swap;src:url('{{ asset('vendor/fonts/inter/inter-latin-400-normal.woff2') }}') format('woff2');unicode-range:U+0000-00FF,U+0131,U+0152-0153,U+02BB-02BC,U+02C6,U+02DA,U+02DC,U+0304,U+0308,U+0329,U+2000-206F,U+20AC,U+2122,U+2191,U+2193,U+2212,U+2215,U+FEFF,U+FFFD}
@font-face{font-family:'Playfair Display';font-style:normal;font-weight:700;font-display:swap;src:url('{{ asset('vendor/fonts/playfair-display/playfair-display-latin-700-normal.woff2') }}') format('woff2');unicode-range:U+0000-00FF,U+0131,U+0152-0153,U+02BB-02BC,U+02C6,U+02DA,U+02DC,U+0304,U+0308,U+0329,U+2000-206F,U+20AC,U+2122,U+2191,U+2193,U+2212,U+2215,U+FEFF,U+FFFD}

/* CSS Variables */
:root{--color-gold:#D4AF37;--color-gold-light:#E2CFA0;--color-gold-dark:#A68B4B;--color-gold-glow:rgba(200,169,110,0.25);--color-silver:#9B9EA3;--color-silver-light:#C5C8CE;--color-silver-dark:#6E7075;--color-black-sweet:#1A1A1E;--color-black-surface:#222226;--color-black-card:#2A2A2F;--color-black-deep:#0F0F12;--color-white:#F5F5F0;--color-white-muted:#D0CFC8;--font-heading:'Playfair Display','Georgia',serif;--font-body:'Inter',system-ui,-apple-system,sans-serif;--shadow-gold:0 0 20px rgba(200,169,110,0.15);--shadow-card:0 4px 20px rgba(0,0,0,0.3);--border-silver:1px solid #C0C0C0;--border-gold:1px solid rgba(200,169,110,0.3);--transition-base:300ms ease;--transition-slow:500ms ease}

/* Base */
html{scroll-behavior:smooth}
body{font-family:var(--font-body);background-color:var(--color-black-deep);color:var(--color-white);line-height:1.7;overflow-x:hidden;margin:0}
h1,h2,h3,h4,h5,h6{font-family:var(--font-heading);color:var(--color-gold);font-weight:700}
a{color:var(--color-gold);text-decoration:none}
p{color:var(--color-white-muted)}
::selection{background-color:var(--color-gold);color:var(--color-black-deep)}

/* Skip link */
.skip-link{position:absolute;top:-100%;left:0;z-index:9999;padding:.75rem 1.5rem;background-color:var(--color-gold);color:var(--color-black-deep);font-weight:600}
.skip-link:focus{top:0}

/* Minimal Bootstrap grid (critical only) */
*,::after,::before{box-sizing:border-box}
.container,.container-fluid{width:100%;padding-right:var(--bs-gutter-x,.75rem);padding-left:var(--bs-gutter-x,.75rem);margin-right:auto;margin-left:auto}
.container{max-width:1320px}
.row{display:flex;flex-wrap:wrap;margin-right:calc(-.5 * var(--bs-gutter-x,1.5rem));margin-left:calc(-.5 * var(--bs-gutter-x,1.5rem))}
.row>*{flex-shrink:0;width:100%;max-width:100%;padding-right:calc(var(--bs-gutter-x,1.5rem) * .5);padding-left:calc(var(--bs-gutter-x,1.5rem) * .5)}
.d-flex{display:flex!important}
.d-none{display:none!important}
.align-items-center{align-items:center!important}
.ms-auto{margin-left:auto!important}
.collapse:not(.show){display:none}
.visually-hidden-focusable:not(:focus):not(:focus-within){position:absolute!important;width:1px!important;height:1px!important;padding:0!important;margin:-1px!important;overflow:hidden!important;clip:rect(0,0,0,0)!important;white-space:nowrap!important;border:0!important}
.sticky-top{position:sticky;top:0;z-index:1020}

/* Navbar */
.navbar{display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;padding:var(--bs-navbar-padding-y,.5rem) var(--bs-navbar-padding-x,0)}
.navbar>.container,.navbar>.container-fluid{display:flex;flex-wrap:inherit;align-items:center;justify-content:space-between}
.navbar-brand{padding-top:var(--bs-navbar-brand-padding-y,.3125rem);padding-bottom:var(--bs-navbar-brand-padding-y,.3125rem);margin-right:var(--bs-navbar-brand-margin-end,1rem);font-size:var(--bs-navbar-brand-font-size,1.25rem);color:var(--bs-navbar-brand-color);text-decoration:none;white-space:nowrap}
.navbar-collapse{flex-basis:100%;flex-grow:1;align-items:center}
.navbar-expand-xl .navbar-collapse{display:flex!important;flex-basis:auto}
.navbar-expand-xl .navbar-toggler{display:none}
.navbar-toggler{padding:var(--bs-navbar-toggler-padding-y,.25rem) var(--bs-navbar-toggler-padding-x,.75rem);font-size:var(--bs-navbar-toggler-font-size,1.25rem);line-height:1;color:var(--bs-navbar-toggler-icon-bg);background-color:transparent;border:var(--bs-border-width,1px) solid var(--bs-navbar-toggler-border-color);border-radius:var(--bs-navbar-toggler-border-radius,.375rem);transition:var(--bs-navbar-toggler-transition,box-shadow .15s ease-in-out)}
.navbar-nav{display:flex;flex-direction:column;padding-left:0;margin-bottom:0;list-style:none}
.nav-link{display:block;padding:var(--bs-nav-link-padding-y,.5rem) var(--bs-nav-link-padding-x,1rem);color:var(--bs-nav-link-color);text-decoration:none}

/* Navbar BK theme */
.navbar-bk{background-color:rgba(15,15,18,.97);backdrop-filter:blur(12px);border-bottom:var(--border-gold);padding-top:0;padding-bottom:0;transition:background-color var(--transition-base)}
.navbar-bk__brand{padding:.5rem 0;margin-right:1.5rem}
.navbar-bk__logo{height:55px;width:auto;display:block;transition:opacity var(--transition-base)}
.navbar-bk__nav{align-items:stretch}
.navbar-bk__link{color:var(--color-silver-light)!important;font-size:.9rem;font-weight:500;letter-spacing:.5px;padding:1.1rem 1rem!important;position:relative;transition:color var(--transition-base),background-color var(--transition-base);display:flex;align-items:center;gap:.2rem;white-space:nowrap}
.navbar-bk__toggler{border-color:var(--color-gold);padding:.35rem .65rem}
.navbar-bk__toggler-icon{color:var(--color-gold);font-size:1.25rem}
.navbar-bk__auth-link{color:var(--color-silver-light);font-size:.85rem;font-weight:500;text-decoration:none;padding:.45rem .9rem;border-radius:.4rem;transition:all var(--transition-base);white-space:nowrap}
.navbar-bk__auth-btn{background:linear-gradient(135deg,var(--color-gold),#b8943d);color:var(--color-black-deep);font-size:.82rem;font-weight:600;text-decoration:none;padding:.45rem 1rem;border-radius:.4rem;transition:all var(--transition-base);white-space:nowrap}
.navbar-bk__search-btn{display:flex;align-items:center;justify-content:center;width:36px;height:36px;border-radius:50%;color:var(--color-silver-light);background:rgba(200,169,110,.08);border:1px solid rgba(200,169,110,.15);text-decoration:none;font-size:.9rem;transition:all .2s ease}
.navbar-bk__user-dropdown{background-color:var(--color-black-sweet);border:var(--border-gold);min-width:200px}

/* Hero */
.hero{background-color:var(--color-black-sweet);position:relative;overflow:hidden;min-height:28vh;display:flex;align-items:center;justify-content:center;text-align:center;border-bottom:var(--border-gold);padding:1.5rem 0}
.hero__overlay{position:absolute;inset:0;background:radial-gradient(ellipse at center,rgba(200,169,110,.08) 0%,transparent 70%);pointer-events:none}
.hero__title{font-size:clamp(1.4rem,3.5vw,2.5rem);color:var(--color-gold);letter-spacing:4px;text-transform:uppercase;margin-bottom:.4rem;text-shadow:0 0 40px rgba(200,169,110,.3)}
.hero__subtitle{font-family:var(--font-heading);font-size:clamp(.8rem,1.8vw,1.1rem);color:var(--color-silver-light);letter-spacing:2px;font-weight:400;margin-bottom:.25rem}
.hero__tagline{font-size:clamp(.75rem,1.2vw,.9rem);color:var(--color-gold-light);font-style:italic;letter-spacing:1.5px}
.hero__divider{width:60px;height:2px;background:linear-gradient(90deg,transparent,var(--color-gold),transparent);margin:.75rem auto}
.hero__description{max-width:550px;margin:0 auto;font-size:.85rem;color:var(--color-white-muted);line-height:1.6}

/* Page Header (inner pages) */
.page-header{background-color:var(--color-black-surface);border-bottom:var(--border-silver);padding:3.5rem 0;text-align:center}
.page-header__inner{max-width:720px;margin:0 auto}
.page-header__title{font-family:var(--font-heading);font-size:2rem;font-weight:700;color:var(--color-gold);letter-spacing:2px;text-transform:uppercase;margin-bottom:1rem}
.page-header__divider{width:80px;height:2px;background:linear-gradient(90deg,transparent,var(--color-gold),transparent);margin:0 auto 1.25rem}

/* Section */
.section{padding:4rem 0}
.section--dark{background-color:var(--color-black-sweet)}
.section--deeper{background-color:var(--color-black-deep)}
.section--surface{background-color:var(--color-black-surface)}

/* Responsive navbar */
@media(max-width:1199.98px){
.navbar-bk{padding-top:.5rem;padding-bottom:.5rem}
.navbar-bk__logo{height:45px}
.navbar-bk__link{padding:.75rem 1rem!important}
.navbar-bk__auth{padding:.75rem 1rem;border-top:1px solid rgba(200,169,110,.12);margin-top:.5rem;width:100%;justify-content:center!important}
.navbar-expand-xl .navbar-collapse{display:none!important}
.navbar-expand-xl .navbar-collapse.show,.navbar-expand-xl .navbar-collapse.collapsing{display:block!important}
.navbar-expand-xl .navbar-toggler{display:flex}
.navbar-expand-xl .navbar-nav{flex-direction:column}
.px-lg-5{padding-right:1rem!important;padding-left:1rem!important}
}
@media(min-width:1200px){
.navbar-expand-xl{flex-wrap:nowrap;justify-content:flex-start}
.navbar-expand-xl .navbar-nav{flex-direction:row}
.ms-xl-3{margin-left:1rem!important}
.px-lg-5{padding-right:3rem!important;padding-left:3rem!important}
}
.px-3{padding-right:1rem!important;padding-left:1rem!important}
.gap-2{gap:.5rem!important}
.dropdown-toggle::after{display:inline-block;margin-left:.255em;vertical-align:.255em;content:"";border-top:.3em solid;border-right:.3em solid transparent;border-bottom:0;border-left:.3em solid transparent}
.dropdown-menu{position:absolute;z-index:1000;display:none;min-width:var(--bs-dropdown-min-width,10rem);padding:var(--bs-dropdown-padding-y,.5rem) 0;margin:0;font-size:var(--bs-dropdown-font-size,1rem);color:var(--bs-dropdown-color);text-align:left;list-style:none;background-color:var(--bs-dropdown-bg,#fff);background-clip:padding-box;border:var(--bs-border-width,1px) solid var(--bs-dropdown-border-color,rgba(0,0,0,.175));border-radius:var(--bs-dropdown-border-radius,.375rem)}
.dropdown-menu-dark{--bs-dropdown-bg:var(--color-black-sweet);--bs-dropdown-border-color:rgba(200,169,110,.3);color:var(--color-silver-light)}
.dropdown-item{display:block;width:100%;padding:var(--bs-dropdown-item-padding-y,.25rem) var(--bs-dropdown-item-padding-x,1rem);clear:both;font-weight:400;color:var(--bs-dropdown-link-color,#212529);text-align:inherit;text-decoration:none;white-space:nowrap;background-color:transparent;border:0}
.position-relative{position:relative!important}
.text-center{text-align:center!important}
.mb-0{margin-bottom:0!important}
.mb-1{margin-bottom:.25rem!important}
.me-1{margin-right:.25rem!important}
.me-2{margin-right:.5rem!important}
.mt-3{margin-top:1rem!important}

/* Hero animation */
@keyframes fadeInUp{from{opacity:0;transform:translateY(30px)}to{opacity:1;transform:translateY(0)}}
.animate-fadein{animation:fadeInUp .6s ease forwards;opacity:0}
.animate-fadein--delay-1{animation-delay:.1s}
.animate-fadein--delay-2{animation-delay:.2s}
.animate-fadein--delay-3{animation-delay:.3s}
.animate-fadein--delay-4{animation-delay:.4s}
.hero__pattern{position:absolute;inset:0;opacity:.03;pointer-events:none}
@media(max-width:991.98px){.hero{min-height:22vh}}
</style>
