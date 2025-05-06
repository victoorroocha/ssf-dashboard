/**
 * DevExtreme (ui/data_grid/ui.data_grid.columns_resizing_reordering.js)
 * Version: 20.2.13
 * Build date: Fri Apr 07 2023
 *
 * Copyright (c) 2012 - 2023 Developer Express Inc. ALL RIGHTS RESERVED
 * Read about DevExtreme licensing here: https://js.devexpress.com/Licensing/
 */
"use strict";
exports.TrackerView = exports.TablePositionViewController = exports.DraggingHeaderViewController = exports.DraggingHeaderView = exports.ColumnsSeparatorView = exports.ColumnsResizerViewController = void 0;
var _uiData_grid = _interopRequireDefault(require("./ui.data_grid.core"));
var _uiGrid_core = _interopRequireDefault(require("../grid_core/ui.grid_core.columns_resizing_reordering"));

function _interopRequireDefault(obj) {
    return obj && obj.__esModule ? obj : {
        "default": obj
    }
}
var DraggingHeaderView = _uiGrid_core.default.views.draggingHeaderView;
exports.DraggingHeaderView = DraggingHeaderView;
var DraggingHeaderViewController = _uiGrid_core.default.controllers.draggingHeader;
exports.DraggingHeaderViewController = DraggingHeaderViewController;
var ColumnsSeparatorView = _uiGrid_core.default.views.columnsSeparatorView;
exports.ColumnsSeparatorView = ColumnsSeparatorView;
var TablePositionViewController = _uiGrid_core.default.controllers.tablePosition;
exports.TablePositionViewController = TablePositionViewController;
var ColumnsResizerViewController = _uiGrid_core.default.controllers.columnsResizer;
exports.ColumnsResizerViewController = ColumnsResizerViewController;
var TrackerView = _uiGrid_core.default.views.trackerView;
exports.TrackerView = TrackerView;
_uiData_grid.default.registerModule("columnsResizingReordering", _uiGrid_core.default);
