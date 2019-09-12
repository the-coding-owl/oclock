define(['jquery'],function($){
    let Clock = {
        selector: '.tx_oclock',
        serverTimeSelector: '.server-time',
        browserTimeSelector: '.browser-time',
        serverTimeZoneSelector: '.server-timezone',
        browserTimeZoneSelector: '.browser-timezone',
        init: function() {
            let clock = this,
                containers = $(clock.selector);
            containers.each(function(){
                let container = $(this),
                    elements = container.find(clock.serverTimeSelector),
                    date = new Date(container.data('time'));
                clock.setTimeZone(date, container.find(clock.serverTimeZoneSelector));
                elements.each(function() {
                    let element = $(this),
                        spans = clock.createTimeSpans()
                        date = new Date(container.data('time'));
                    clock.updateTime(date, spans);
                    clock.appendTimes(element, spans);
                    clock.setClock(date, spans);
                });
                let browserElements = container.find(clock.browserTimeSelector);
                date = new Date();
                clock.setTimeZone(date, container.find(clock.browserTimeZoneSelector));
                browserElements.each(function () {
                    let element = $(this),
                        spans = clock.createTimeSpans()
                        date = new Date();
                    clock.updateTime(date, spans);
                    clock.appendTimes(element, spans);
                    clock.setClock(date, spans);
                });
            });
        },
        updateTime: function(date, spans) {
            let hours = date.getHours(),
                minutes = date.getMinutes(),
                seconds = date.getSeconds();
            spans.hour.innerText = hours < 10 ? '0' + hours : hours;
            spans.minute.innerText = minutes < 10 ? '0' + minutes : minutes;
            spans.second.innerText = seconds < 10 ? '0' + seconds : seconds;
        },
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
        setClock: function(date, spans) {
            let clock = this;
            setInterval(function(){
                date.setSeconds(date.getSeconds() + 1);
                clock.updateTime(date, spans);
            }, 1000);
        },
        appendTimes: function(element, spans) {
            element.append(spans.hour);
            element.append(document.createTextNode(':'));
            element.append(spans.minute);
            element.append(document.createTextNode(':'));
            element.append(spans.second);
        },
        setTimeZone: function(date, element) {
            element.text(/\((.*)\)/.exec(date.toString())[1]);
        }
    };
    Clock.init();
    return Clock;
});
