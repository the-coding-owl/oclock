define(['jquery', 'TYPO3/CMS/Oclock/Luxon'], function($, luxon){
    let Clock = {
        selector: '.tx_oclock',
        serverTimeSelector: '.server-time',
        browserTimeSelector: '.browser-time',
        serverTimeZoneSelector: '.server-timezone',
        browserTimeZoneSelector: '.browser-timezone',
        /**
         * Initialize the Clock
         */
        init: function() {
            let clock = this,
                containers = $(clock.selector);
            /**
             * Run through the containers of the oclock extension in the backend
             */
            containers.each(function(){
                let container = $(this),
                    elements = container.find(clock.serverTimeSelector),
                    date = luxon.DateTime.fromRFC2822(
                        container.data('time'),
                        { zone: container.data('timezone') }
                    );
                clock.setTimeZone(date, container.find(clock.serverTimeZoneSelector));
                /**
                 * Run through the elements that contain the timers
                 */
                elements.each(function() {
                    let element = $(this),
                        spans = clock.createTimeSpans()
                        // need to create the time twice, so seconds are not added multiple times when updating times
                        date = luxon.DateTime.fromRFC2822(
                            container.data('time'),
                            { zone: container.data('timezone') }
                        );
                    clock.updateTime(date, spans);
                    clock.appendTimes(element, spans);
                    clock.setClock(date, spans);
                });
                let browserElements = container.find(clock.browserTimeSelector);
                date = luxon.DateTime.local();
                clock.setTimeZone(date, container.find(clock.browserTimeZoneSelector));
                browserElements.each(function () {
                    let element = $(this),
                        spans = clock.createTimeSpans()
                        date = luxon.DateTime.local();
                    clock.updateTime(date, spans);
                    clock.appendTimes(element, spans);
                    clock.setClock(date, spans);
                });
            });
        },
        /**
         * Update the time spans
         *
         * @param {DateTime} date The object that contains the date
         * @param {object} spans An object containing the spans that contain the times
         */
        updateTime: function(date, spans) {
            let hours = date.hour,
                minutes = date.minute,
                seconds = date.second;
            spans.hour.innerText = hours < 10 ? '0' + hours : hours;
            spans.minute.innerText = minutes < 10 ? '0' + minutes : minutes;
            spans.second.innerText = seconds < 10 ? '0' + seconds : seconds;
        },
        /**
         * Create the object with the spans containing the times
         *
         * @return {object}
         */
        createTimeSpans: function() {
            let spans = {
                hour: document.createElement('SPAN'),
                minute: document.createElement('SPAN'),
                second: document.createElement('SPAN')
            };
            spans.hour.classList.add('hour');
            spans.minute.classList.add('minute');
            spans.second.classList.add('second');
            return spans;
        },
        /**
         * Start the actual "clock", this means this starts the interval that updates the time
         *
         * @param {DateTime} date The object containing the Date
         * @param {object} spans An object containing the spans that contain the times
         */
        setClock: function(date, spans) {
            let clock = this;
            setInterval(function(){
                date = date.plus({seconds: 1});
                clock.updateTime(date, spans);
            }, 1000);
        },
        /**
         * Appends the times to the given element
         *
         * @param {jQuery} element The jquery object of the element to append the times to
         * @param {object} spans An object containing the spans that contain the times
         */
        appendTimes: function(element, spans) {
            element.append(spans.hour);
            element.append(document.createTextNode(':'));
            element.append(spans.minute);
            element.append(document.createTextNode(':'));
            element.append(spans.second);
        },
        /**
         * Set the timezone string to the given element
         *
         * @param {DateTime} date The object containing the Date
         * @param {jQuery} element The jquery object of the element to append the timezone string to
         */
        setTimeZone: function(date, element) {
            element.text(date.offsetNameLong);
        }
    };
    Clock.init();
    return Clock;
});
