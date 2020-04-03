class Clock {
    static selector = '.tx_oclock';

    static serverTimeSelector = '.server-time';

    static browserTimeSelector = '.browser-time';

    static serverTimeZoneSelector = '.server-timezone';

    static browserTimeZoneSelector = '.browser-timezone';

    static interval = 1000;

    static instances = [];

    /**
     * Constructor of a Clock object
     *
     * @param {Node} container The Node that represents the clock container
     * @param {Luxon} luxon The luxon date library
     */
    constructor(container, luxon) {
        this.container = container;
        this.serverTimeList = [];
        let elementList = container.querySelectorAll(Clock.serverTimeSelector);
        for (let element of elementList) {
            this.serverTimeList.push({
                element: element
            });
        }

        this.serverTimeZoneList = [];
        elementList = container.querySelectorAll(Clock.serverTimeZoneSelector);
        for (let element of elementList) {
            this.serverTimeZoneList.push({
                element: element
            });
        }

        this.browserTimeList = [];
        elementList = container.querySelectorAll(Clock.browserTimeSelector);
        for (let element of elementList) {
            this.browserTimeList.push({
                element: element
            });
        }

        this.browserTimeZoneList = [];
        elementList = container.querySelectorAll(Clock.browserTimeZoneSelector);
        for (let element of elementList) {
            this.browserTimeZoneList.push({
                element: element
            });
        }

        this.serverTime = luxon.DateTime.fromRFC2822(
            container.dataset.time,
            { zone: container.dataset.timezone }
        );
        this.browserTime = luxon.DateTime.local();

        this.initializeTimeZones();
        this.initializeClocks();

        this.initialized = true;
    }

    /**
     * Initialize the TimeZones
     */
    initializeTimeZones() {
        if (this.serverTimeZoneList.length > 0) {
            for (let serverTimeZone of this.serverTimeZoneList) {
                serverTimeZone.element.innerText = this.serverTime.offsetNameLong;
            }
        }

        if (this.browserTimeZoneList.length > 0) {
            for (let browserTimeZone of this.browserTimeZoneList) {
                browserTimeZone.element.innerText = this.browserTime.offsetNameLong;
            }
        }
    }

    /**
     * Initialize the Clocks
     */
    initializeClocks() {
        if (this.serverTimeList.length > 0) {
            for (let serverTime of this.serverTimeList) {
                serverTime.spans = Clock.createTimeSpans();
                serverTime.time = this.serverTime;
                this.updateTime(serverTime);
                this.appendTimes(serverTime);
                this.startClock(serverTime);
            }
        }

        if (this.browserTimeList.length > 0) {
            for (let browserTime of this.browserTimeList) {
                browserTime.spans = Clock.createTimeSpans();
                browserTime.time = this.browserTime;
                this.updateTime(browserTime);
                this.appendTimes(browserTime);
                this.startClock(browserTime);
            }
        }
    }

    /**
     * Update the times
     *
     * @param {Object} timeObject An object containing the timings
     */
    updateTime(timeObject) {
        let hours = timeObject.time.hour,
            minutes = timeObject.time.minute,
            seconds = timeObject.time.second;
        timeObject.spans.hour.innerText = hours < 10 ? '0' + hours : hours;
        timeObject.spans.minute.innerText = minutes < 10 ? '0' + minutes : minutes;
        timeObject.spans.second.innerText = seconds < 10 ? '0' + seconds : seconds;
    }

    /**
     * Append the time spans to the element
     *
     * @param {Object} timeObject The object containing the time, element and spans
     */
    appendTimes(timeObject) {
        timeObject.element.appendChild(timeObject.spans.hour);
        timeObject.element.appendChild(document.createTextNode(':'));
        timeObject.element.appendChild(timeObject.spans.minute);
        timeObject.element.appendChild(document.createTextNode(':'));
        timeObject.element.appendChild(timeObject.spans.second);
    }

    /**
     * Start the clock of the given time object
     *
     * @param {Object} timeObject The object containing the time, element and spans
     */
    startClock(timeObject) {
        let instance = this;
        timeObject.interval = setInterval(function(){
            timeObject.time = timeObject.time.plus({seconds: 1});
            instance.updateTime(timeObject);
        }, Clock.interval);
    }

    /**
     * Stop the clock of the given time object
     *
     * @param {Object} timeObject The object containing the time, element and spans
     */
    stopClock(timeObject) {
        clearInterval(timeObject.interval);
    }

    /**
     * @return {Array}
     */
    getServerTimes() {
        return this.serverTimeList;
    }

    /**
     * @return {Array}
     */
    getBrowserTimes() {
        return this.browserTimeList;
    }

    /**
     * @return {Array}
     */
    getServerTimeZones() {
        return this.serverTimeZoneList;
    }

    /**
     * @return {Array}
     */
    getBrowserTimeZones() {
        return this.browserTimeZoneList;
    }

    /**
     * @return {Boolean}
     */
    isInitialized(container) {
        if (this.container === container && this.initialized === true) {
            return true;
        }

        return false;
    }

    /**
     * Initialize the all Clock elements
     *
     * @param {Luxon} luxon The luxon date library
     */
    static init(luxon) {
        let containers = document.querySelectorAll(Clock.selector);
        for (let container of containers) {
            if (!this.isInitialized(container)) {
                this.instances.push(new Clock(container, luxon));
            }
        }
    }

    /**
     * Check if the container is initialized already
     *
     * @param {Node} container The container to check
     * @return {Boolean}
     */
    static isInitialized(container) {
        for (let instance of Clock.instances) {
            if (instance.isInitialized(container)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Create the object with the spans containing the times
     *
     * @return {Object}
     */
    static createTimeSpans() {
        let spans = {
            hour: document.createElement('SPAN'),
            minute: document.createElement('SPAN'),
            second: document.createElement('SPAN')
        };
        spans.hour.classList.add('hour');
        spans.minute.classList.add('minute');
        spans.second.classList.add('second');
        return spans;
    }
}

define(['TYPO3/CMS/Oclock/Luxon'], function (luxon) {
    Clock.init(luxon);

    /*eslint-disable max-depth*/
    const observer = new MutationObserver(function(mutations) {
        for (let mutation of mutations) {
            if (mutation.type === 'childList' && mutation.target.classList.contains('widget-content') && mutation.addedNodes.length > 0) {
                for (let node of mutation.addedNodes) {
                    if (node.nodeType === Node.ELEMENT_NODE && node.classList.contains('tx-thecodingowl-oclock')) {
                        for (let childNode of node.childNodes) {
                            if (childNode.nodeType === Node.ELEMENT_NODE && childNode.classList.contains('tx_oclock')) {
                                if (!Clock.isInitialized(childNode)) {
                                    Clock.instances.push(new Clock(childNode, luxon));
                                }
                            }
                        }
                    }
                }
            }
        }
    });
    /*eslint-enable max-depth*/

    observer.observe(document, {
        subtree: true,
        childList: true
    });

    return Clock;
});
