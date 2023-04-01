<p align="center"><a href="https://fraction.anir.cloud" target="_blank"><img src="https://github.com/fraction-framework/fraction/blob/main/images/logo.png?raw=true" width="300" alt="Fraction Logo"></a></p>

<p align="center">
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/fraction-framework/fraction" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/fraction-framework/fraction" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/fraction-framework/fraction" alt="License"></a>
</p>

# Fraction Framework

Fraction is an attribute-based framework. The main perpose of Fraction is to provide a simple tool for building
self-documenting RESTful APIs, wheareas it can be used for building any kind of web application.
Note that Fraction is still in development and is not recommended for production use. Many features are still missing.

## Documentation

For a comprehensive guide on how to use Fraction Framework, detailed information on its features, and best practices,
please visit the official [Fraction Framework documentation](https://fraction.anir.cloud).

## Installation

To create a new project with Fraction, open your terminal and navigate to the directory where you want to create your
project. Run the following command:

```bash
composer create-project fraction-framework/starter your_project_name
```

Replace `your_project_name` with the desired name for your project. This command will create a new directory with the
specified project name and install the latest version of Fraction and its dependencies.

Alternatively, you can install Fraction as a dependency in an existing project. To do so, run the following command:

```bash
composer require fraction-framework/fraction
```

## Basic Usage

Creating your first API endpoint is as simple as follows:

```php
#[Route(RequestMethod::GET, '/')]
#[View(response: ResponseType::JSON)]
public function index(): array {
  return ['message' => 'Hello World!'];
}
```

For more information on how to use Fraction, please refer to the [documentation](https://fraction.anir.cloud).

## Demo Application

To see Fraction Framework in action, we have provided a demo application showcasing its various features and
functionalities. You can find the demo application [here](https://github.com/fraction-framework/demo-app). This will
help you better understand the framework's structure and provide a solid starting point for building your own
applications using Fraction.

## License

Fraction is licensed under the [MIT License](LICENSE).

