define([],function(){
    let Clock = {
        selector: '.tx_oclock_time',
        init: function() {
            let element = document.querySelectorAll(this.selector);
            for(let index in element) {
                if (typeof element[index] !== 'object') {
                    continue;
                }

                let timestamp = element[index].dataset.time,
                    date = new Date(timestamp);
                    spans = {
                        hour: document.createElement('SPAN'),
                        minute: document.createElement('SPAN'),
                        second: document.createElement('SPAN')
                    },
                    hours = date.getHours(),
                    minutes = date.getMinutes(),
                    seconds = date.getSeconds();
                spans.hour.innerText = hours < 10 ? '0' + hours : hours;
                spans.minute.innerText = minutes < 10 ? '0' + minutes : minutes;
                spans.second.innerText = seconds < 10 ? '0' + seconds : seconds;
                spans.hour.classList.add('hour');
                spans.minute.classList.add('minute');
                spans.second.classList.add('second');
                element[index].appendChild(spans.hour);
                element[index].appendChild(document.createTextNode(':'));
                element[index].appendChild(spans.minute);
                element[index].appendChild(document.createTextNode(':'));
                element[index].appendChild(spans.second);
                setInterval(function(){
                    date.setSeconds(date.getSeconds() + 1);
                    let hours = date.getHours(),
                        minutes = date.getMinutes(),
                        seconds = date.getSeconds();
                    spans.hour.innerText = hours < 10 ? '0' + hours : hours;
                    spans.minute.innerText = minutes < 10 ? '0' + minutes : minutes;
                    spans.second.innerText = seconds < 10 ? '0' + seconds : seconds;
                }, 1000);
            }
        }
    };
    Clock.init();
    return Clock;
});
