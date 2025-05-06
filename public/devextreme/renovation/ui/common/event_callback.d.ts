/**
* DevExtreme (renovation/ui/common/event_callback.d.ts)
* Version: 20.2.13
* Build date: Fri Apr 07 2023
*
* Copyright (c) 2012 - 2023 Developer Express Inc. ALL RIGHTS RESERVED
* Read about DevExtreme licensing here: https://js.devexpress.com/Licensing/
*/
/**
 * Warning! This type is used for internal purposes. Do not import it directly.
 */
export type EventCallback<T = void> = T extends void ? () => void : (value: T) => void;
