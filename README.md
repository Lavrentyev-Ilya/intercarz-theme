# InterCarz — WordPress тема-оболочка

Тёплая профессиональная тема для каталога автозапчастей. Реализует **визуальную оболочку** (header, footer, корзина, checkout, аккаунт) по дизайн-системе [`DESIGN.md`](./DESIGN.md). Внутренние страницы каталога и flow подбора (Brand → Model → Engine → Section → Part) рисует **встраиваемое приложение CPMod**, которое грузит WordPress через `wp-load.php` и оборачивает свой контент темой.

## Требования

- WordPress 6.5+ (рассчитано на 7.0), PHP 8.0+
- **WooCommerce** — корзина / checkout / my-account
- **CURCY — Multi Currency for WooCommerce** (VillaTheme) — валютный свитчер, cookie `wmc_current_currency`
- **Polylang** — мультиязычность и свитчер языка
- **CPMod** (CarParts) — само приложение каталога

## Структура

```
style.css            заголовок темы (стилей нет — см. assets/css)
theme.json           дизайн-токены для блочного редактора
functions.php        bootstrap, подключает inc/
inc/
  setup.php          supports, меню, intercarz_is_app_context()
  enqueue.php        стили/скрипты (Inter, токены, theme.css, main.js)
  template-tags.php  branding, меню, свитчеры, аккаунт, контакты
  woocommerce.php    мини-корзина, AJAX-фрагменты, мост AxajAddCartDOM()
  customizer.php     цвета, шапка, контакты, соцсети, оплата
  cpt.php            CPT: слайды, отзывы, бренды (+ метабоксы)
  home.php           витрина главной: Customizer + рендер секций
header.php / footer.php   контракт с приложением (см. ниже)
front-page.php       главная страница (витрина)
index/page/singular/404.php   обычные WP-страницы вне приложения
assets/css/tokens.css     переменные дизайн-системы (источник правды)
assets/css/theme.css      стили оболочки
assets/css/home.css       стили витрины главной (только front-page)
assets/js/main.js         меню, выпадающая корзина
assets/js/home.js         слайдеры главной (герой, отзывы)
```

## Главная страница (витрина)

`front-page.php` собирает секции по мотивам Bumbleb в нашей дизайн-системе.
Управление — **Внешний вид → Настроить → «Главная страница»** (тумблеры,
заголовки, счётчики, баннеры) + контент:

| Секция | Источник контента |
|---|---|
| Герой-слайдер | CPT **Слайды** (фон = изображение записи) |
| Категории | категории товаров WooCommerce |
| Промо-баннеры (2 и 3) | Customizer (изображение/заголовок/кнопка) |
| Хиты продаж | WooCommerce `[best_selling_products]` |
| Спецпредложения | WooCommerce `[sale_products]` |
| Отзывы | CPT **Отзывы** |
| Преимущества | Customizer (3 пункта) |
| Бренды | CPT **Бренды** (логотип = изображение записи) |
| Блог | последние записи |
| Подписка | Customizer (+ шорткод формы) |

Чтобы витрина показалась, в **Настройки → Чтение** главной должна быть наша
страница (или оставить вывод последних записей — `front-page.php` имеет
приоритет). Маршруты приложения CPMod при этом не затрагиваются.

## Контракт с приложением CPMod

Файл интеграции CPMod (`WordPress.WC.php`) делает:

1. `require wp-load.php` → поднимает WordPress.
2. Кладёт товар в корзину через `WC()->cart->add_to_cart()` (создавая `product` на лету).
3. Вызывает `get_header()` и `get_footer()` активной темы.
4. Выводит свой HTML внутри `<div style="grid-area:breadcrumbs">…$CarMod_Content…</div>`.
5. До/после страницы вызывает `AxajAddCartDOM()`.

Тема обеспечивает совместимость так:

| Что ждёт приложение | Где в теме |
|---|---|
| `get_header()` открывает обёртку контента | `header.php` → `<div class="site-content is-app">` |
| контейнер с grid-областью `breadcrumbs` | `.site-content.is-app { display:grid; grid-template-areas:"breadcrumbs" }` в `theme.css` |
| `get_footer()` закрывает обёртку | `footer.php` закрывает `.site-content` |
| AJAX-обновление корзины | элемент `#cp-cart` в шапке (см. ниже) |
| корзина/checkout | WooCommerce + `add_theme_support('woocommerce')` |
| валюта `wmc_current_currency` | свитчер CURCY в шапке |

**Корзина (`AxajAddCartDOM`).** Функцию определяет **ядро CPMod** (`core/funcs.php`), тема её НЕ объявляет. После add-to-cart CPMod буферизирует весь рендер страницы, парсит его `DOMDocument`, находит элемент с `id = CMS_CART_HTML_DOM_ID` и возвращает только его innerHTML — JS приложения подменяет им корзину в шапке. Поэтому тема просто выводит элемент `#cp-cart`, внутри которого лежит всё обновляемое (счётчик, сумма, позиции). **В админке CPMod установите `CMS_CART_HTML_DOM_ID = cp-cart`** (пустое поле → CPMod делает редирект на страницу корзины вместо AJAX).

**Детект контекста приложения** — `intercarz_is_app_context()` (наличие `$CPMod` / констант `CM_*`). В контексте приложения обёртка контента — на полную ширину (приложение само центрирует свои блоки); на обычных WP/Woo-страницах — контейнер `1280px`.

### Точки расширения (фильтры)

- `intercarz_is_app_context` — переопределить детект контекста приложения.

## Дизайн-токены

Все значения из `DESIGN.md` лежат в `assets/css/tokens.css` (CSS-переменные `--accent-500`, `--ink-900`, `--space-6` …) и продублированы в `theme.json` для редактора. Шрифт — **Inter** (`tnum` включён для табличных цифр).

## TODO / не сделано

- Не протестировано на живом WP (локально нет PHP) — нужен прогон на стенде.
- Реальные логотип/контакты — через «Внешний вид → Настроить → Контакты и подвал».
- В настройках CPMod задать `CMS_CART_HTML_DOM_ID = cp-cart` (иначе add-to-cart будет редиректить на корзину, а не обновлять её AJAX-ом).
- В CPMod отключить `CMS_INCLUDE_JQUERY` — jQuery уже подключает WordPress (через `wc-cart-fragments`), иначе он загрузится дважды.
- Self-hosted шрифт вместо Google Fonts (перф/GDPR) — при необходимости.
