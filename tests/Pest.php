<?php

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "pest()" function to bind a different classes or traits.
|
*/

pest()->extend(Tests\TestCase::class)
    ->use(Illuminate\Foundation\Testing\RefreshDatabase::class)
    ->in('Feature', 'Unit');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/

expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});

/*
|--------------------------------------------------------------------------
| Functions & Test Helpers
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the number of lines of code in your test files.
|
*/

/**
 * Set APP_ADMIN environment variable for tests.
 * Accepts array/int/string and writes to putenv, $_ENV, and $_SERVER.
 */
function setAdminsEnv($ids): void
{
    if (is_array($ids)) {
        $value = implode(',', array_map(fn($v) => (string)$v, $ids));
    } else {
        $value = trim((string)$ids);
    }

    // Set the env vars for any direct env() usage during bootstrap
    putenv('APP_ADMIN=' . $value);
    $_ENV['APP_ADMIN'] = $value;
    $_SERVER['APP_ADMIN'] = $value;

    // Also update runtime config so tests can change admin list per test
    config(['app.admin' => $value]);
}

beforeEach(function () {
    // Ensure a clean slate for each test regarding admin list
    setAdminsEnv('');
});
