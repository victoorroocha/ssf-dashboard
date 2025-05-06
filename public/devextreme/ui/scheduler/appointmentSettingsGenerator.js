/**
 * DevExtreme (ui/scheduler/appointmentSettingsGenerator.js)
 * Version: 20.2.13
 * Build date: Fri Apr 07 2023
 *
 * Copyright (c) 2012 - 2023 Developer Express Inc. ALL RIGHTS RESERVED
 * Read about DevExtreme licensing here: https://js.devexpress.com/Licensing/
 */
"use strict";
exports.AppointmentSettingsGeneratorVirtualStrategy = exports.AppointmentSettingsGeneratorBaseStrategy = exports.AppointmentSettingsGenerator = void 0;
var _date = _interopRequireDefault(require("../../core/utils/date"));
var _type = require("../../core/utils/type");
var _extend = require("../../core/utils/extend");
var _recurrence = require("./recurrence");
var _utilsTimeZone = _interopRequireDefault(require("./utils.timeZone.js"));

function _interopRequireDefault(obj) {
    return obj && obj.__esModule ? obj : {
        "default": obj
    }
}

function _typeof(obj) {
    "@babel/helpers - typeof";
    return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function(obj) {
        return typeof obj
    } : function(obj) {
        return obj && "function" == typeof Symbol && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj
    }, _typeof(obj)
}

function _toConsumableArray(arr) {
    return _arrayWithoutHoles(arr) || _iterableToArray(arr) || _unsupportedIterableToArray(arr) || _nonIterableSpread()
}

function _nonIterableSpread() {
    throw new TypeError("Invalid attempt to spread non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.")
}

function _iterableToArray(iter) {
    if ("undefined" !== typeof Symbol && null != iter[Symbol.iterator] || null != iter["@@iterator"]) {
        return Array.from(iter)
    }
}

function _arrayWithoutHoles(arr) {
    if (Array.isArray(arr)) {
        return _arrayLikeToArray(arr)
    }
}

function _inheritsLoose(subClass, superClass) {
    subClass.prototype = Object.create(superClass.prototype);
    subClass.prototype.constructor = subClass;
    _setPrototypeOf(subClass, superClass)
}

function _setPrototypeOf(o, p) {
    _setPrototypeOf = Object.setPrototypeOf ? Object.setPrototypeOf.bind() : function(o, p) {
        o.__proto__ = p;
        return o
    };
    return _setPrototypeOf(o, p)
}

function _slicedToArray(arr, i) {
    return _arrayWithHoles(arr) || _iterableToArrayLimit(arr, i) || _unsupportedIterableToArray(arr, i) || _nonIterableRest()
}

function _nonIterableRest() {
    throw new TypeError("Invalid attempt to destructure non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.")
}

function _unsupportedIterableToArray(o, minLen) {
    if (!o) {
        return
    }
    if ("string" === typeof o) {
        return _arrayLikeToArray(o, minLen)
    }
    var n = Object.prototype.toString.call(o).slice(8, -1);
    if ("Object" === n && o.constructor) {
        n = o.constructor.name
    }
    if ("Map" === n || "Set" === n) {
        return Array.from(o)
    }
    if ("Arguments" === n || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)) {
        return _arrayLikeToArray(o, minLen)
    }
}

function _arrayLikeToArray(arr, len) {
    if (null == len || len > arr.length) {
        len = arr.length
    }
    for (var i = 0, arr2 = new Array(len); i < len; i++) {
        arr2[i] = arr[i]
    }
    return arr2
}

function _iterableToArrayLimit(arr, i) {
    var _i = null == arr ? null : "undefined" != typeof Symbol && arr[Symbol.iterator] || arr["@@iterator"];
    if (null != _i) {
        var _s, _e, _x, _r, _arr = [],
            _n = !0,
            _d = !1;
        try {
            if (_x = (_i = _i.call(arr)).next, 0 === i) {
                if (Object(_i) !== _i) {
                    return
                }
                _n = !1
            } else {
                for (; !(_n = (_s = _x.call(_i)).done) && (_arr.push(_s.value), _arr.length !== i); _n = !0) {}
            }
        } catch (err) {
            _d = !0, _e = err
        } finally {
            try {
                if (!_n && null != _i.return && (_r = _i.return(), Object(_r) !== _r)) {
                    return
                }
            } finally {
                if (_d) {
                    throw _e
                }
            }
        }
        return _arr
    }
}

function _arrayWithHoles(arr) {
    if (Array.isArray(arr)) {
        return arr
    }
}

function ownKeys(object, enumerableOnly) {
    var keys = Object.keys(object);
    if (Object.getOwnPropertySymbols) {
        var symbols = Object.getOwnPropertySymbols(object);
        enumerableOnly && (symbols = symbols.filter(function(sym) {
            return Object.getOwnPropertyDescriptor(object, sym).enumerable
        })), keys.push.apply(keys, symbols)
    }
    return keys
}

function _objectSpread(target) {
    for (var i = 1; i < arguments.length; i++) {
        var source = null != arguments[i] ? arguments[i] : {};
        i % 2 ? ownKeys(Object(source), !0).forEach(function(key) {
            _defineProperty(target, key, source[key])
        }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(target, Object.getOwnPropertyDescriptors(source)) : ownKeys(Object(source)).forEach(function(key) {
            Object.defineProperty(target, key, Object.getOwnPropertyDescriptor(source, key))
        })
    }
    return target
}

function _defineProperty(obj, key, value) {
    key = _toPropertyKey(key);
    if (key in obj) {
        Object.defineProperty(obj, key, {
            value: value,
            enumerable: true,
            configurable: true,
            writable: true
        })
    } else {
        obj[key] = value
    }
    return obj
}

function _defineProperties(target, props) {
    for (var i = 0; i < props.length; i++) {
        var descriptor = props[i];
        descriptor.enumerable = descriptor.enumerable || false;
        descriptor.configurable = true;
        if ("value" in descriptor) {
            descriptor.writable = true
        }
        Object.defineProperty(target, _toPropertyKey(descriptor.key), descriptor)
    }
}

function _createClass(Constructor, protoProps, staticProps) {
    if (protoProps) {
        _defineProperties(Constructor.prototype, protoProps)
    }
    if (staticProps) {
        _defineProperties(Constructor, staticProps)
    }
    Object.defineProperty(Constructor, "prototype", {
        writable: false
    });
    return Constructor
}

function _toPropertyKey(arg) {
    var key = _toPrimitive(arg, "string");
    return "symbol" === _typeof(key) ? key : String(key)
}

function _toPrimitive(input, hint) {
    if ("object" !== _typeof(input) || null === input) {
        return input
    }
    var prim = input[Symbol.toPrimitive];
    if (void 0 !== prim) {
        var res = prim.call(input, hint || "default");
        if ("object" !== _typeof(res)) {
            return res
        }
        throw new TypeError("@@toPrimitive must return a primitive value.")
    }
    return ("string" === hint ? String : Number)(input)
}
var toMs = _date.default.dateToMilliseconds;
var AppointmentSettingsGenerator = function() {
    function AppointmentSettingsGenerator(scheduler) {
        this.scheduler = scheduler;
        this.settingsStrategy = this.scheduler.isVirtualScrolling() ? new AppointmentSettingsGeneratorVirtualStrategy(this.scheduler) : new AppointmentSettingsGeneratorBaseStrategy(this.scheduler)
    }
    var _proto = AppointmentSettingsGenerator.prototype;
    _proto.create = function(rawAppointment) {
        return this.settingsStrategy.create(rawAppointment)
    };
    return AppointmentSettingsGenerator
}();
exports.AppointmentSettingsGenerator = AppointmentSettingsGenerator;
var AppointmentSettingsGeneratorBaseStrategy = function() {
    function AppointmentSettingsGeneratorBaseStrategy(scheduler) {
        this.scheduler = scheduler
    }
    var _proto2 = AppointmentSettingsGeneratorBaseStrategy.prototype;
    _proto2.create = function(rawAppointment) {
        var scheduler = this.scheduler;
        var appointment = scheduler.createAppointmentAdapter(rawAppointment);
        var itemResources = scheduler._resourcesManager.getResourcesFromItem(rawAppointment);
        var isAllDay = this._isAllDayAppointment(rawAppointment);
        var appointmentList = this._createAppointments(appointment, itemResources);
        appointmentList = this._getProcessedByAppointmentTimeZone(appointmentList, appointment);
        if (this._canProcessNotNativeTimezoneDates(appointment)) {
            appointmentList = this._getProcessedNotNativeTimezoneDates(appointmentList, appointment)
        }
        var gridAppointmentList = this._createGridAppointmentList(appointmentList, appointment);
        gridAppointmentList = this._cropAppointmentsByStartDayHour(gridAppointmentList, rawAppointment, isAllDay);
        gridAppointmentList = this._getProcessedLongAppointmentsIfRequired(gridAppointmentList, appointment);
        var appointmentInfos = this._createAppointmentInfos(gridAppointmentList, itemResources, isAllDay, appointment.isRecurrent);
        return appointmentInfos
    };
    _proto2._getProcessedByAppointmentTimeZone = function(appointmentList, appointment) {
        var _this = this;
        var hasAppointmentTimeZone = !(0, _type.isEmptyObject)(appointment.startDateTimeZone) || !(0, _type.isEmptyObject)(appointment.endDateTimeZone);
        if (appointmentList.length > 1 && hasAppointmentTimeZone) {
            var appointmentOffsets = {
                startDate: this.timeZoneCalculator.getOffsets(appointment.startDate, appointment.startDateTimeZone),
                endDate: this.timeZoneCalculator.getOffsets(appointment.endDate, appointment.endDateTimeZone)
            };
            appointmentList.forEach(function(a) {
                var sourceOffsets = {
                    startDate: _this.timeZoneCalculator.getOffsets(a.startDate, appointment.startDateTimeZone),
                    endDate: _this.timeZoneCalculator.getOffsets(a.endDate, appointment.endDateTimeZone)
                };
                var startDateOffsetDiff = appointmentOffsets.startDate.appointment - sourceOffsets.startDate.appointment;
                var endDateOffsetDiff = appointmentOffsets.endDate.appointment - sourceOffsets.endDate.appointment;
                if (sourceOffsets.startDate.appointment !== sourceOffsets.startDate.common) {
                    a.startDate = new Date(a.startDate.getTime() + startDateOffsetDiff * toMs("hour"))
                }
                if (sourceOffsets.endDate.appointment !== sourceOffsets.endDate.common) {
                    a.endDate = new Date(a.endDate.getTime() + endDateOffsetDiff * toMs("hour"))
                }
            })
        }
        return appointmentList
    };
    _proto2._isAllDayAppointment = function(rawAppointment) {
        return this.scheduler.appointmentTakesAllDay(rawAppointment) && this.workspace.supportAllDayRow()
    };
    _proto2._createAppointments = function(appointment, resources) {
        var appointments = this._createRecurrenceAppointments(appointment, resources);
        if (!appointment.isRecurrent && 0 === appointments.length) {
            appointments.push({
                startDate: appointment.startDate,
                endDate: appointment.endDate
            })
        }
        appointments = appointments.map(function(item) {
            var _item$endDate;
            var resultEndTime = null === (_item$endDate = item.endDate) || void 0 === _item$endDate ? void 0 : _item$endDate.getTime();
            if (item.startDate.getTime() === resultEndTime) {
                item.endDate.setTime(resultEndTime + toMs("minute"))
            }
            return _objectSpread(_objectSpread({}, item), {}, {
                exceptionDate: new Date(item.startDate)
            })
        });
        return appointments
    };
    _proto2._canProcessNotNativeTimezoneDates = function(appointment) {
        var timeZoneName = this.scheduler.option("timeZone");
        var isTimeZoneSet = !(0, _type.isEmptyObject)(timeZoneName);
        if (!isTimeZoneSet) {
            return false
        }
        if (!appointment.isRecurrent) {
            return false
        }
        return !_utilsTimeZone.default.isEqualLocalTimeZone(timeZoneName, appointment.startDate)
    };
    _proto2._getProcessedNotNativeDateIfCrossDST = function(date, offset) {
        if (offset < 0) {
            var newDate = new Date(date);
            var newDateMinusOneHour = new Date(newDate);
            newDateMinusOneHour.setHours(newDateMinusOneHour.getHours() - 1);
            var newDateOffset = this.timeZoneCalculator.getOffsets(newDate).common;
            var newDateMinusOneHourOffset = this.timeZoneCalculator.getOffsets(newDateMinusOneHour).common;
            if (newDateOffset !== newDateMinusOneHourOffset) {
                return 0
            }
        }
        return offset
    };
    _proto2._getCommonOffset = function(date) {
        return this.timeZoneCalculator.getOffsets(date).common
    };
    _proto2._getProcessedNotNativeTimezoneDates = function(appointmentList, appointment) {
        var _this2 = this;
        return appointmentList.map(function(item) {
            var diffStartDateOffset = _this2._getCommonOffset(appointment.startDate) - _this2._getCommonOffset(item.startDate);
            var diffEndDateOffset = _this2._getCommonOffset(appointment.endDate) - _this2._getCommonOffset(item.endDate);
            if (0 === diffStartDateOffset && 0 === diffEndDateOffset) {
                return item
            }
            diffStartDateOffset = _this2._getProcessedNotNativeDateIfCrossDST(item.startDate, diffStartDateOffset);
            diffEndDateOffset = _this2._getProcessedNotNativeDateIfCrossDST(item.endDate, diffEndDateOffset);
            var newStartDate = new Date(item.startDate.getTime() + diffStartDateOffset * toMs("hour"));
            var newEndDate = new Date(item.endDate.getTime() + diffEndDateOffset * toMs("hour"));
            var testNewStartDate = _this2.timeZoneCalculator.createDate(newStartDate, {
                path: "toGrid"
            });
            var testNewEndDate = _this2.timeZoneCalculator.createDate(newEndDate, {
                path: "toGrid"
            });
            if (appointment.duration > testNewEndDate.getTime() - testNewStartDate.getTime()) {
                newEndDate = new Date(newStartDate.getTime() + appointment.duration)
            }
            return _objectSpread(_objectSpread({}, item), {}, {
                startDate: newStartDate,
                endDate: newEndDate,
                exceptionDate: new Date(newStartDate)
            })
        })
    };
    _proto2._getProcessedLongAppointmentsIfRequired = function(gridAppointmentList, appointment) {
        var _this3 = this;
        var rawAppointment = appointment.source();
        var allDay = this.scheduler.appointmentTakesAllDay(rawAppointment);
        var dateRange = this.workspace.getDateRange();
        var renderingStrategy = this.scheduler.getLayoutManager().getRenderingStrategyInstance();
        if (renderingStrategy.needSeparateAppointment(allDay)) {
            var longStartDateParts = [];
            var resultDates = [];
            gridAppointmentList.forEach(function(gridAppointment) {
                var maxDate = new Date(dateRange[1]);
                var endDateOfPart = renderingStrategy.normalizeEndDateByViewEnd(rawAppointment, gridAppointment.endDate);
                longStartDateParts = _date.default.getDatesOfInterval(gridAppointment.startDate, endDateOfPart, {
                    milliseconds: _this3.scheduler.getWorkSpace().getIntervalDuration(allDay)
                });
                var list = longStartDateParts.filter(function(startDatePart) {
                    return new Date(startDatePart) < maxDate
                }).map(function(date) {
                    return {
                        startDate: date,
                        endDate: new Date(new Date(date).setMilliseconds(appointment.duration)),
                        source: gridAppointment.source
                    }
                });
                resultDates = resultDates.concat(list)
            });
            gridAppointmentList = resultDates
        }
        return gridAppointmentList
    };
    _proto2._createGridAppointmentList = function(appointmentList, appointment) {
        var _this4 = this;
        return appointmentList.map(function(source) {
            var offsetDifference = appointment.startDate.getTimezoneOffset() - source.startDate.getTimezoneOffset();
            if (0 !== offsetDifference && _this4._canProcessNotNativeTimezoneDates(appointment)) {
                source.startDate = new Date(source.startDate.getTime() + offsetDifference * toMs("minute"));
                source.endDate = new Date(source.endDate.getTime() + offsetDifference * toMs("minute"));
                source.exceptionDate = new Date(source.startDate)
            }
            var startDate = _this4.timeZoneCalculator.createDate(source.startDate, {
                path: "toGrid"
            });
            var endDate = _this4.timeZoneCalculator.createDate(source.endDate, {
                path: "toGrid"
            });
            return {
                startDate: startDate,
                endDate: endDate,
                source: source
            }
        })
    };
    _proto2._createExtremeRecurrenceDates = function(rawAppointment) {
        var dateRange = this.workspace.getDateRange();
        var startViewDate = this.scheduler.appointmentTakesAllDay(rawAppointment) ? _date.default.trimTime(dateRange[0]) : dateRange[0];
        var endViewDate = dateRange[1];
        var commonTimeZone = this.scheduler.option("timeZone");
        if (commonTimeZone) {
            startViewDate = this.timeZoneCalculator.createDate(startViewDate, {
                path: "fromGrid"
            });
            endViewDate = this.timeZoneCalculator.createDate(endViewDate, {
                path: "fromGrid"
            });
            var daylightOffset = _utilsTimeZone.default.getDaylightOffsetInMs(startViewDate, endViewDate);
            if (daylightOffset) {
                endViewDate = new Date(endViewDate.getTime() + daylightOffset)
            }
        }
        return [startViewDate, endViewDate]
    };
    _proto2._createRecurrenceOptions = function(appointment, groupIndex) {
        var _this5 = this;
        var _this$_createExtremeR = this._createExtremeRecurrenceDates(appointment.source(), groupIndex),
            _this$_createExtremeR2 = _slicedToArray(_this$_createExtremeR, 2),
            minRecurrenceDate = _this$_createExtremeR2[0],
            maxRecurrenceDate = _this$_createExtremeR2[1];
        return {
            rule: appointment.recurrenceRule,
            exception: appointment.recurrenceException,
            min: minRecurrenceDate,
            max: maxRecurrenceDate,
            firstDayOfWeek: this.scheduler.getFirstDayOfWeek(),
            start: appointment.startDate,
            end: appointment.endDate,
            getPostProcessedException: function(date) {
                var timeZoneName = _this5.scheduler.option("timeZone");
                if ((0, _type.isEmptyObject)(timeZoneName) || _utilsTimeZone.default.isEqualLocalTimeZone(timeZoneName, date)) {
                    return date
                }
                var appointmentOffset = _this5.timeZoneCalculator.getOffsets(appointment.startDate).common;
                var exceptionAppointmentOffset = _this5.timeZoneCalculator.getOffsets(date).common;
                var diff = appointmentOffset - exceptionAppointmentOffset;
                diff = _this5._getProcessedNotNativeDateIfCrossDST(date, diff);
                return new Date(date.getTime() - diff * _date.default.dateToMilliseconds("hour"))
            }
        }
    };
    _proto2._createRecurrenceAppointments = function(appointment, resources) {
        var duration = appointment.duration;
        var option = this._createRecurrenceOptions(appointment);
        var generatedStartDates = (0, _recurrence.getRecurrenceProcessor)().generateDates(option);
        return generatedStartDates.map(function(date) {
            var utcDate = _utilsTimeZone.default.createUTCDateWithLocalOffset(date);
            utcDate.setTime(utcDate.getTime() + duration);
            var endDate = _utilsTimeZone.default.createDateFromUTCWithLocalOffset(utcDate);
            return {
                startDate: new Date(date),
                endDate: endDate
            }
        })
    };
    _proto2._getGroupIndices = function(resources) {
        var workspace = this.scheduler._workSpace;
        return workspace._getGroupIndexes(resources)
    };
    _proto2._cropAppointmentsByStartDayHour = function(appointments, rawAppointment, isAllDay) {
        var _this6 = this;
        return appointments.map(function(appointment) {
            var startDate = new Date(appointment.startDate);
            var firstViewDate = _this6._getAppointmentFirstViewDate(appointment, rawAppointment);
            var startDayHour = _this6._getViewStartDayHour(firstViewDate);
            appointment.startDate = _this6._getAppointmentResultDate({
                appointment: appointment,
                rawAppointment: rawAppointment,
                startDate: startDate,
                startDayHour: startDayHour,
                firstViewDate: firstViewDate
            });
            return appointment
        })
    };
    _proto2._getAppointmentFirstViewDate = function() {
        return this.scheduler.getStartViewDate()
    };
    _proto2._getViewStartDayHour = function() {
        return this.scheduler._getCurrentViewOption("startDayHour")
    };
    _proto2._getAppointmentResultDate = function(options) {
        var appointment = options.appointment,
            rawAppointment = options.rawAppointment,
            startDayHour = options.startDayHour,
            firstViewDate = options.firstViewDate;
        var startDate = options.startDate;
        var resultDate = new Date(appointment.startDate);
        if (this.scheduler.appointmentTakesAllDay(rawAppointment)) {
            resultDate = _date.default.normalizeDate(startDate, firstViewDate)
        } else {
            if (startDate < firstViewDate) {
                startDate = firstViewDate
            }
            resultDate = _date.default.normalizeDate(appointment.startDate, startDate)
        }
        return _date.default.roundDateByStartDayHour(resultDate, startDayHour)
    };
    _proto2._createAppointmentInfos = function(gridAppointments, resources, allDay, recurrent) {
        var _this7 = this;
        var result = [];
        var _loop = function(i) {
            var coordinates = _this7.scheduler._workSpace.getCoordinatesByDateInGroup(gridAppointments[i].startDate, resources, allDay);
            coordinates.forEach(function(coordinate) {
                (0, _extend.extend)(coordinate, {
                    info: {
                        appointment: gridAppointments[i],
                        sourceAppointment: gridAppointments[i].source
                    }
                })
            });
            result = result.concat(coordinates)
        };
        for (var i = 0; i < gridAppointments.length; i++) {
            _loop(i)
        }
        return result
    };
    _createClass(AppointmentSettingsGeneratorBaseStrategy, [{
        key: "timeZoneCalculator",
        get: function() {
            return this.scheduler.timeZoneCalculator
        }
    }, {
        key: "workspace",
        get: function() {
            return this.scheduler.getWorkSpace()
        }
    }, {
        key: "viewDataProvider",
        get: function() {
            return this.workspace.viewDataProvider
        }
    }]);
    return AppointmentSettingsGeneratorBaseStrategy
}();
exports.AppointmentSettingsGeneratorBaseStrategy = AppointmentSettingsGeneratorBaseStrategy;
var AppointmentSettingsGeneratorVirtualStrategy = function(_AppointmentSettingsG) {
    _inheritsLoose(AppointmentSettingsGeneratorVirtualStrategy, _AppointmentSettingsG);

    function AppointmentSettingsGeneratorVirtualStrategy() {
        return _AppointmentSettingsG.apply(this, arguments) || this
    }
    var _proto3 = AppointmentSettingsGeneratorVirtualStrategy.prototype;
    _proto3._createAppointmentInfos = function(gridAppointments, resources, allDay, recurrent) {
        var _this8 = this;
        var appointments = allDay ? gridAppointments : gridAppointments.filter(function(item) {
            var source = item.source,
                startDate = item.startDate,
                endDate = item.endDate;
            var groupIndex = source.groupIndex;
            return _this8.viewDataProvider.isGroupIntersectDateInterval(groupIndex, startDate, endDate)
        });
        if (recurrent && this.isVerticalGrouping) {
            return this._createRecurrentAppointmentInfos(appointments, resources, allDay)
        }
        return _AppointmentSettingsG.prototype._createAppointmentInfos.call(this, appointments, resources, allDay, recurrent)
    };
    _proto3._createRecurrentAppointmentInfos = function(gridAppointments, resources, allDay) {
        var _this9 = this;
        var result = [];
        gridAppointments.forEach(function(appointment) {
            var source = appointment.source;
            var groupIndex = source.groupIndex;
            var coordinate = _this9.workspace.getCoordinatesByDate(appointment.startDate, groupIndex, allDay);
            if (coordinate) {
                (0, _extend.extend)(coordinate, {
                    info: {
                        appointment: appointment,
                        sourceAppointment: source
                    }
                });
                result.push(coordinate)
            }
        });
        return result
    };
    _proto3._cropAppointmentsByStartDayHour = function(appointments, rawAppointment, isAllDay) {
        var _this10 = this;
        return appointments.filter(function(appointment) {
            var firstViewDate = _this10._getAppointmentFirstViewDate(appointment, rawAppointment);
            if (!firstViewDate) {
                return false
            }
            var startDayHour = _this10._getViewStartDayHour(firstViewDate);
            var startDate = new Date(appointment.startDate);
            appointment.startDate = _this10._getAppointmentResultDate({
                appointment: appointment,
                rawAppointment: rawAppointment,
                startDate: startDate,
                startDayHour: startDayHour,
                firstViewDate: firstViewDate
            });
            return !isAllDay ? appointment.endDate > appointment.startDate : true
        })
    };
    _proto3._createRecurrenceAppointments = function(appointment, resources) {
        var _this11 = this;
        var duration = appointment.duration;
        var result = [];
        var groupIndices = this.isVerticalGrouping && this.workspace._getGroupCount() ? this._getGroupIndices(resources) : [0];
        groupIndices.forEach(function(groupIndex) {
            var option = _this11._createRecurrenceOptions(appointment, groupIndex);
            var generatedStartDates = (0, _recurrence.getRecurrenceProcessor)().generateDates(option);
            var recurrentInfo = generatedStartDates.map(function(date) {
                var startDate = new Date(date);
                var utcDate = _utilsTimeZone.default.createUTCDateWithLocalOffset(date);
                utcDate.setTime(utcDate.getTime() + duration);
                var endDate = _utilsTimeZone.default.createDateFromUTCWithLocalOffset(utcDate);
                return {
                    startDate: startDate,
                    endDate: endDate,
                    groupIndex: groupIndex
                }
            });
            result.push.apply(result, _toConsumableArray(recurrentInfo))
        });
        return result
    };
    _proto3._getViewStartDayHour = function(firstViewDate) {
        return firstViewDate.getHours()
    };
    _proto3._getAppointmentFirstViewDate = function(appointment, rawAppointment) {
        var _this$scheduler$getWo = this.scheduler.getWorkSpace(),
            viewDataProvider = _this$scheduler$getWo.viewDataProvider;
        var groupIndex = appointment.source.groupIndex;
        var startDate = appointment.startDate,
            endDate = appointment.endDate;
        var isAllDay = this._isAllDayAppointment(rawAppointment);
        return viewDataProvider.findGroupCellStartDate(groupIndex, startDate, endDate, isAllDay)
    };
    _proto3._updateGroupIndices = function(appointments, itemResources) {
        var _this12 = this;
        var groupIndices = this.isVerticalGrouping ? this._getGroupIndices(itemResources) : [0];
        var result = [];
        groupIndices.forEach(function(groupIndex) {
            var groupStartDate = _this12.viewDataProvider.getGroupStartDate(groupIndex);
            if (groupStartDate) {
                appointments.forEach(function(appointment) {
                    var appointmentCopy = (0, _extend.extend)({}, appointment);
                    appointmentCopy.groupIndex = groupIndex;
                    result.push(appointmentCopy)
                })
            }
        });
        return result
    };
    _proto3._getGroupIndices = function(resources) {
        var groupIndices = _AppointmentSettingsG.prototype._getGroupIndices.call(this, resources);
        var _this$scheduler$getWo2 = this.scheduler.getWorkSpace(),
            viewDataProvider = _this$scheduler$getWo2.viewDataProvider;
        var viewDataGroupIndices = viewDataProvider.getGroupIndices();
        var result = groupIndices.filter(function(groupIndex) {
            return viewDataGroupIndices.indexOf(groupIndex) !== -1
        });
        return result
    };
    _proto3._createAppointments = function(appointment, resources) {
        var appointments = _AppointmentSettingsG.prototype._createAppointments.call(this, appointment, resources);
        return !appointment.isRecurrent ? this._updateGroupIndices(appointments, resources) : appointments
    };
    _createClass(AppointmentSettingsGeneratorVirtualStrategy, [{
        key: "viewDataProvider",
        get: function() {
            return this.workspace.viewDataProvider
        }
    }, {
        key: "isVerticalGrouping",
        get: function() {
            return this.workspace._isVerticalGroupedWorkSpace()
        }
    }]);
    return AppointmentSettingsGeneratorVirtualStrategy
}(AppointmentSettingsGeneratorBaseStrategy);
exports.AppointmentSettingsGeneratorVirtualStrategy = AppointmentSettingsGeneratorVirtualStrategy;
