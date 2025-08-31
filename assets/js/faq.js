var Accordion = function(el, multiple) {
    this.el = el || {};
    this.multiple = multiple || false;

    var links = this.el.find('.aximo-accordion-header');
    links.on('click', {
        el: this.el,
        multiple: this.multiple
    }, this.dropdown);
}

Accordion.prototype.dropdown = function(e) {
    var $el = e.data.el;
    var $this = jQuery(this),
        $next = $this.next();

    $next.slideToggle();
    $this.parent().toggleClass('open');

    if (!e.data.multiple) {
        $el.find('.aximo-accordion-body').not($next).slideUp().parent().removeClass('open');
    }
};

var accordion = new Accordion(jQuery('.aximo-accordion-wrap'), false);
