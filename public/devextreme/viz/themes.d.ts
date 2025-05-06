/**
* DevExtreme (viz/themes.d.ts)
* Version: 20.2.13
* Build date: Fri Apr 07 2023
*
* Copyright (c) 2012 - 2023 Developer Express Inc. ALL RIGHTS RESERVED
* Read about DevExtreme licensing here: https://js.devexpress.com/Licensing/
*/


/**
 * Gets the current theme's name.
 */
export function currentTheme(): string;

/**
 * Changes the current theme for all data visualization UI components on the page. The color scheme is defined separately.
 */
export function currentTheme(platform: string, colorScheme: string): void;

/**
 * Changes the current theme for all data visualization UI components on the page.
 */
export function currentTheme(theme: string): void;

/**
 * Gets a predefined or registered theme's settings.
 */
export function getTheme(theme: string): any;

/**
 * Refreshes the current theme and palette in all data visualization UI components on the page.
 */
export function refreshTheme(): void;

/**
 * Registers a new theme based on the existing one.
 */
export function registerTheme(customTheme: any, baseTheme: string): void;