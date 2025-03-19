/**
* DevExtreme (ui/track_bar.d.ts)
* Version: 20.2.13
* Build date: Fri Apr 07 2023
*
* Copyright (c) 2012 - 2023 Developer Express Inc. ALL RIGHTS RESERVED
* Read about DevExtreme licensing here: https://js.devexpress.com/Licensing/
*/
import Editor, {
    EditorOptions
} from './editor/editor';

/**
 * Warning! This type is used for internal purposes. Do not import it directly.
 */
export interface dxTrackBarOptions<T = dxTrackBar> extends EditorOptions<T> {
    /**
     * The maximum value the UI component can accept.
     */
    max?: number;
    /**
     * The minimum value the UI component can accept.
     */
    min?: number;
}
/**
 * A base class for track bar UI components.
 * Warning! This type is used for internal purposes. Do not import it directly.
 */
export default class dxTrackBar extends Editor {
    constructor(element: Element, options?: dxTrackBarOptions)
    constructor(element: JQuery, options?: dxTrackBarOptions)
}
