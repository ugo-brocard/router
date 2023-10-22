# Router

This **Composer package** simplifies route management in your PHP applications by allowing the use of **PHP attributes** to define **routes** in an **elegant and expressive** way. It streamlines the setup of **custom routes**, enabling developers to define routing rules that are **clear and intuitive**.

## ✨ Features

- [x] Define routes using PHP attributes: **enhancing code readability and maintainability**. 
- [x] Flexible handling of route parameters and URL constraints.
- [x] Seamless integration with popular PHP frameworks.
- [x] Route group management **for hierarchical organization of routes**.
- [x] Middleware support **for advanced request processing customization**.
- [x] Comprehensive documentation and usage examples **for quick adoption**.

## 🔗 Installation

You can install this package via Composer:
```bash
composer require ugo-brocard/router
```

## 🧱 Usage

Here's a basic example of how to use this package to define a route using PHP attributes:
```php
use Router\Attributes\{Route, Get, Post}

/**
 * Class MyController
 * 
 * @package Application\Controllers
*/
#[Route("/route-group")]
final class MyController
{
    #[Get("/route")]
    public function myAction(): string
    {
        // (...)
    }

    #[Post("/route")]
    public function yetAnotherAction(): int
    {
        // (...)
    }
}
```

For detailed documentation and usage examples, please refer to our [documentation](https://github.com/ugo-brocard/router/wiki).

## 🛡 License

This project is licensed under the [MIT License](https://github.com/ugo-brocard/router/blob/main/LICENSE).

## 🤠 Credits

This package was developed and maintained by [Ugo Brocard](https://github.com/ugo-brocard).

## 💖 Acknowledgments

I'd like to express my gratitude to the **open-source community** for their **contributions and inspirations**
