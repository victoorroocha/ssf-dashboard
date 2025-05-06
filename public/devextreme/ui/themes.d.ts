/**
* DevExtreme (ui/themes.d.ts)
* Version: 20.2.13
* Build date: Fri Apr 07 2023
*
* Copyright (c) 2012 - 2023 Developer Express Inc. ALL RIGHTS RESERVED
* Read about DevExtreme licensing here: https://js.devexpress.com/Licensing/
*/
/**
 * An object that serves as a namespace for the methods that work with DevExtreme CSS Themes.
 */
export default class themes {
    /**
     * Gets the current theme's name.
     */
    static current(): string;
    /**
     * Sets a theme with a specific name.
     */
    static current(themeName: string): void;
    /**
     * Specifies a function to be executed each time a theme is switched.
     */
    static ready(callback: Function): void;
    /**
     * Specifies a function to be executed after a theme is loaded.
     */
    static initialized(callback: Function): void;
    static isMaterial(theme: string): boolean;
}

/**
 * Warning! This type is used for internal purposes. Do not import it directly.
 */
export function current(): string;
/**
 * Warning! This type is used for internal purposes. Do not import it directly.
 */
export function isMaterial(theme: string): boolean;
