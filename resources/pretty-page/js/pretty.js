(function () {
    function callFunction(func) {
        func();
    }

    function toggle(event) {
        let container = this;

        let element = event.target;
        do {
            if (element.classList.contains('code')) {
                return;
            }
            element = element.parentNode;
        } while (element && element.classList);

        if(!container.classList.contains('active')) {
            container.classList.add('active');

            Prism.plugins.lineHighlight.highlightLines(container.querySelector('.code'))();

            return;
        }

        container.classList.remove('active');
    }

    for (let element of document.getElementsByClassName('backtrace-item')) {
        element.onclick = toggle;
    }
}());