<script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />


{{--Diese style unten sind meine individuellen, der hauptstyle befindet sich in der daterangepicker.css--}}

<style>
    .daterangepicker select.monthselect,
    .daterangepicker select.yearselect,
    .daterangepicker select.hourselect,
    .daterangepicker select.minuteselect,
    .daterangepicker select.secondselect,
    .daterangepicker select.ampmselect{
        font-size: 12px; /* Anpassung der Schriftgröße */
        padding: 2px 10px 2px 10px; /* Anpassung des Innenabstands */
        background-color: #f3f4f6; /* Hintergrundfarbe */
        border-radius: 6px; /* Abrundung der Ecken */
        height: auto; /* Automatische Höhe für bessere Anpassung */
        margin: 0; /* Entfernung des Außenabstands */
        cursor: default; /* Standard-Cursor */
        border:none;
    }

    .dark .daterangepicker select.monthselect,
    .dark .daterangepicker select.yearselect,
    .dark .daterangepicker select.hourselect,
    .dark .daterangepicker select.minuteselect,
    .dark .daterangepicker select.secondselect,
    .dark .daterangepicker select.ampmselect{
        background-color: #1C2332; /* Hintergrundfarbe */
        border: solid 1px #343B4B; /* Rahmenfarbe */
        color: #9CA3AF;
    }

    .dark .daterangepicker .calendar-table .prev span,
    .dark .daterangepicker .calendar-table .next span {
        color: #fff;
        border: solid #9CA3AF;
        border-width: 0 2px 2px 0;
        border-radius: 0;
        display: inline-block;
        padding: 3px;
    }

    /*Select Inputs*/
    /*.daterangepicker select.monthselect, .daterangepicker select.yearselect {*/
    /*    font-size: 12px;*/
    /*    padding: 1px;*/
    /*    background-color: #e5e7eb;*/
    /*    border-radius: 4px;*/
    /*    height: auto;*/
    /*    margin: 0;*/
    /*    cursor: default;*/
    /*}*/

    .daterangepicker select.monthselect {
        margin-right: 2%;
        width: 56%;
    }

    .daterangepicker select.yearselect {
        width: 40%;
    }

    /*.daterangepicker select.hourselect, .daterangepicker select.minuteselect, .daterangepicker select.secondselect, .daterangepicker select.ampmselect {*/
    /*    width: 50px;*/
    /*    margin: 0 auto;*/
    /*    background: #eee;*/
    /*    border: 1px solid #eee;*/
    /*    padding: 2px;*/
    /*    outline: 0;*/
    /*    font-size: 12px;*/
    /*}*/


    .daterangepicker .ranges li:hover {
        background-color: #f3f4f6 !important;
    }

    .dark .daterangepicker .ranges li:hover {
        background-color: rgba(55, 65, 81, 0.5) !important;
    }

    .daterangepicker .ranges li.active {
        background-color: #4f46e5 !important;
        color: #fff;
    }

    .dark .daterangepicker .ranges li.active {
        background-color: #6366f1 !important;
        color: #fff;
    }

    .dark .daterangepicker {
        color: #9ca3af; /* Ganzes Div DateRange Picker, hier kannst du die bg ground ändern */
        background-color: #1f2937;
        border: 1px solid #374151;
        box-shadow: rgba(149, 157, 165, 0.2) 0 0 0 0;
    }

    .dark .daterangepicker .drp-calendar.left {
        border-left: 1px solid #374151 !important; /* Border left, die ich zwei Tage gesucht habe */
    }

    .dark .daterangepicker .calendar-table {
        background-color: #1f2937; /* HIntergrundfrabe des Calenders rechts der aufpoppt */
        border: 1px solid #1f2937;
    }

    /* weiss nicht was das ist*/
    /*.dark .daterangepicker:before, .dark .daterangepicker:after {*/
    /*    position: absolute;*/
    /*    display: inline-block;*/
    /*    border-bottom-color: #CC7832;*/
    /*    content: '';*/
    /*}*/


    .dark .daterangepicker:before {
        top: -7px;
        border-right: 7px solid transparent;
        border-left: 7px solid transparent;
        border-bottom: 7px solid #374151;
    }

    .dark .daterangepicker:after {
        top: -6px;
        border-right: 6px solid transparent;
        border-bottom: 6px solid #1f2937;
        border-left: 6px solid transparent;
    }

    .dark .daterangepicker td.available:hover, .dark .daterangepicker th.available:hover {
        background-color: rgba(55, 65, 81, 0.9) !important;
        color: #e2e8f0;
        font-size: 14px !important;
    }

    .daterangepicker td.available:hover, .daterangepicker th.available:hover, .daterangepicker td.in-range.off:hover {
        background-color: #e5e7eb !important;
        color: #e2e8f0;
        font-size: 14px !important;
    }

    .daterangepicker td.off.start-date{
        background-color: #d1d5db !important;
        color: #374151 !important;
    }

    /*.daterangepicker td.off.start-date, .dark .daterangepicker td.off.end-date {*/
    /*    background-color: #c3e6cb !important;*/
    /*    color: #FFFFFF;*/
    /*}*/

    .daterangepicker td.off {
        background-color: #f9fafb !important;
        color: #4b5563;
    }

    .dark .daterangepicker td.off.in-range:hover {
        background-color: rgba(55, 65, 81, 0.9) !important;
        color: #e2e8f0;
        font-size: 14px !important;
    }

    /*.dark .daterangepicker td.off.in-range {*/
    /*    background-color:#d1d5db !important;*/
    /*    color: #4b5563 !important;*/
    /*}*/

    .dark .daterangepicker td.off {
        background-color: rgba(26, 32, 44, 0.5) !important;
        color: #4b5563;
    }

    .dark .daterangepicker td.off.in-range  {
        background-color: rgba(55, 65, 81, 0.2)!important;
        font-weight: bolder !important;
        color: #9CA3AF !important;
    }

    .dark .daterangepicker td.off.end-date  {
        background-color: rgba(26, 32, 44, 0.5)!important;
        font-weight: bolder !important;
        color: #FFFFFF !important;
    }

    .daterangepicker td.off.in-range  {
        background-color:rgb(243,244,246)!important;
        font-weight: bolder !important;
        color: #111827 !important;
    }

    .dark .daterangepicker td.in-range  {
        background-color: rgba(55, 65, 81, 0.5) !important;
        font-weight: bolder !important;
        color: #e2e8f0 !important;
    }

    .daterangepicker td.in-range  {
        background-color: rgb(229,231,235, 0.7) !important;
        font-weight: bolder !important;
        color: #1f2937 !important;
    }
    /* Anfang: Calender Datum Start End */
    .dark .daterangepicker td.active, .dark .daterangepicker td.active:hover {
        background-color: #6366f1 !important;
        color: #ffffff;
        border-color: transparent;
    }

    .dark .daterangepicker td.active.off{
        background-color: rgba(255, 255, 255, 0.1) !important;
        color: #ffffff !important;
        font-weight: bolder !important;
    }

    .daterangepicker td.active.off, .daterangepicker td.active.off:hover{
        background-color: #d1d5db !important;
        color: #374151 !important;
        font-weight: bolder !important;
    }


    .daterangepicker td.active, .daterangepicker td.active:hover {
        background-color: #4f46e5 !important;
        border-color: transparent;
        color: #FFFFFF !important;
    }

    /* Ende: Calender Datum Start End */

    /* Footer Linie bei den Buttons  */
    .dark .daterangepicker .drp-buttons {
        border-top: 1px solid #374151;
    }


    .daterangepicker {
        font-family: Figtree, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica Neue, Arial, Noto Sans, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", Segoe UI Symbol, "Noto Color Emoji" !important;
        box-shadow: rgba(149, 157, 165, 0.2) 0 8px 24px; /* Schatten erstellt */
    }



    .daterangepicker td.available:hover, .daterangepicker th.available:hover {
        background-color: #fff;
        border-color: transparent;
        color: inherit;
    }



    /*.dark .daterangepicker td.available:hover, .dark .daterangepicker th.available:hover, .dark .daterangepicker td.in-range.off:hover {*/
    /*    background-color: rgba(55, 65, 81, 0.9) !important;*/
    /*    color: #e2e8f0;*/
    /*    font-size: 14px !important;*/
    /*}*/

    .daterangepicker .drp-buttons {
        clear: both;
        text-align: right;
        padding: 8px;
        border-top: 1px solid #ddd;
        display: none;
        line-height: 12px;
        vertical-align: middle;
    }

    .daterangepicker .drp-selected {
        display: inline-block;
        font-size: 12px;
        padding-right: 8px;
    }

    /* Buttons Start */
    /* Prmary Button */
    .dark .daterangepicker .drp-buttons .applyBtn {
        border-radius: 4px;
        border: none;
        color: white;
        background-color: #6366f1;
    }
    /* Cancel Button */
    .dark .daterangepicker .drp-buttons .cancelBtn {
        /*border: 1px solid #E2E8F0;*/
        border:none;
        background-color: rgba(255, 255, 255, 0.1);
        color: #FFFFFF;
    }

    /* Hover Cancel Button */
    .dark .daterangepicker .drp-buttons .cancelBtn:hover {
        background-color: rgba(255, 255, 255, 0.2);
    }
    /* Hover Primary Button */
    .dark .daterangepicker .drp-buttons .applyBtn:hover{
        background-color: #818cf8;
    }
    /* Buttons end */

    /* Button CSS start */
    .daterangepicker .drp-buttons .btn {
        margin-left: 8px;
        font-size: 12px;
        font-weight: bold;
        border-radius: 4px;
        box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        cursor: pointer;
        transition: background-color 0.2s;
    }

    /* Prmary Button */
    .daterangepicker .drp-buttons .applyBtn {
        border-radius: 4px;
        border: 1px solid #4c51bf;
        color: white;
        background-color: #4c51bf;
    }
    /* Cancel Button */
    .daterangepicker .drp-buttons .cancelBtn {
        border: 1px solid #d1d5db;
        background-color: #ffffff;
        color: #1f2937;
    }

    /* Hover Cancel Button */
    .daterangepicker .drp-buttons .cancelBtn:hover {
        background-color: #f9fafb;
    }
    /* Hover Primary Button */
    .daterangepicker .drp-buttons .applyBtn:hover{
        /*background-color: #0088cc;*/
        background-color: #667eea;
    }

    .daterangepicker .drp-buttons .btn:disabled {
        opacity: 0.5;
        pointer-events: none;
    }
    /* Button CSS end */
</style>



<div
    x-data="{
        value: [moment(), moment()],
        init() {
            $(this.$refs.picker).daterangepicker({
                startDate: this.value[0],
                endDate: this.value[1],
                label: this.value[2],
                locale: {
                    format: 'DD.MM.YYYY',
                    separator: ' - ',
                    applyLabel: 'Bestätigen',
                    cancelLabel: 'Abbrechen',
                    fromLabel: 'Von',
                    toLabel: 'Bis',
                    customRangeLabel: 'Custom',
                    weekLabel: 'W',
                    daysOfWeek: [
                        'SO','Mo','DI','MI','DO','Fr','Sa'
                    ],
                    monthNames: ['Januar','Februar','März','April','Mai','Juni','Juli','August','September','Oktober','November','Dezember'
                    ],
                    firstDay: 1
                },

                ranges: {
                    'Heute': [moment(), moment()],
                    'Letzten 7 Tage': [moment().subtract(6, 'days'), moment()],
                    'Letzten 30 Tage': [moment().subtract(29, 'days'), moment()],
                    'Letzten 3 Monate': [moment().subtract(89, 'days'), moment()],
                    'Dieses Jahr': [moment().startOf('year'), moment().endOf('year')],
                    'Alle': [moment().subtract(10, 'years'), moment()]
                },
            },
            (start, end, label) => {
                this.$wire.set('filters.start',  start.format('MM/DD/YYYY'));
                    this.$wire.set('filters.end', end.format('MM/DD/YYYY'));
                         this.$wire.set('filters.range', label);

            })

            this.$watch('value', () => {
                $(this.$refs.picker).data('daterangepicker').setStart(this.value[0])
                $(this.$refs.picker).data('daterangepicker').setEnd(this.value[1])
            })
        },
    }"
    class="max-w-sm w-full"
>
    <div class="w-full relative flex items-center gap-2 rounded-md text-gray-600 text-sm">
        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
            <x-pupi.icon.calendar/>
        </div>
        <input type="text" x-ref="picker" readonly class="min-w-60 py-2.5 px-3 pl-12 block w-full text-sm font-medium
        bg-white border-0 ring-1 ring-inset ring-gray-300 shadow-sm align-middle rounded-md hover:bg-gray-50 focus:ring-2 focus:ring-inset focus:ring-indigo-600 dark:focus:ring-indigo-500 dark:bg-white/5 dark:text-gray-400 dark:ring-gray-700/50 dark:hover:bg-gray-800 dark:hover:text-white cursor-pointer">
</div>
</div>
