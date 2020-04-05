![lint](https://github.com/the-coding-owl/oclock/workflows/lint/badge.svg)

# Introduction
This extension adds a simple clock to the TYPO3 backend, which can be used to see what time and timezone it is on the server and in the browser.

# Installation
Simply find the extension in the TYPO3 extension repoyitory via the extension manager in the backend or visit https://extensions.typo3.org and download it from there.
It is also possible to install it via composer, if your project is composer enabled, by typing `composer require the-coding-owl/oclock`.

# Configuration
There is no configuration available at this point.

# Usage
For displaying the times and timezones, you do not need to configure anything.
The information will be displayed in the top toolbar of the TYPO3 backend.
Time will be ticking automatically, but be aware that it will not be synced with the server.
So keep in mind, that if you suspend your computer, it will probably stop the timer and will go out og sync
because of that. Simply reload your backend in that case.

# Contribution
If you find any issues, please file bug reports at https://github.com/the-coding-owl/oclock/issues.
You can also contribute code, by opening pull requests on github.
For feature requests, please also use the github issue tracker.

# Cudos
Cudos to the guys from Luxon (https://moment.github.io/luxon/index.html), because they enable JavaScript to work
with Timezones.
