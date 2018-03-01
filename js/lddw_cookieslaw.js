/**
 * 2018 http://www.la-dame-du-web.com
 *
 * @author    Nicolas PETITJEAN <n.petitjean@la-dame-du-web.com>
 * @copyright 2018 Nicolas PETITJEAN
 * @license MIT License
 */

setCookieNotice = function(element, options) {
    this.box = element;
    this.close_button = element.find('.lddw-cookie-close');
    this.agree_button = element.find('#lddw-cookie-agree');

    // Manage options
    var def_opts = {
        direction: 'left',
        expire: 'week',
        domain: ''
    };
    this.options = jQuery.extend({}, def_opts, options);

    // Catch width & height
    var width = this.box.outerWidth();
    var height = this.box.outerHeight();
    this.box.data('width', width);
    this.box.data('height', height);

    // Close action
    var self = this;
    this.close_button.on('click', function() {
        self.set_cookie('accept');
    });

    // Agree action
    this.agree_button.on('click', function() {
        self.set_cookie('accept');
    });
}
setCookieNotice.prototype = {

    set_cookie: function(cookie_value) {

        var cnTime = new Date(),
            cnLater = new Date(),
            self = this,
            cookie_duration;

        switch (this.options.expire) {
            default:
            case 'day':
                cookie_duration = 86400;
                break;
            case 'week':
                cookie_duration = 86400 * 7;
                break;
            case 'month':
                cookie_duration = 86400 * 30;
                break;
            case 'year':
                cookie_duration = 86400 * 7 * 52.25;
                break;
        }

        // Set expiry time in seconds
        cnLater.setTime(parseInt(cnTime.getTime()) + cookie_duration * 1000);

        // Set cookie
        cookie_value = cookie_value === 'accept';
        document.cookie = 'cookie_notice_accepted=' + cookie_value + ';expires=' + cnLater.toGMTString() + ';domain=' + this.options.domain + ';path=/';

        console.log(document.cookie);
        // Hide message container
        self.close();
    },

    close: function() {
        var self = this,
            distance;
        if(this.options.direction == 'left') {
            distance = -1.1 * this.box.data('width');
            this.box.animate({left: distance}, 300, function() {
                self.box.remove();
            });
        } else {
            distance = -1.1 * this.box.data('height');
            this.box.animate({bottom: distance}, 300, function() {
                self.box.remove();
            });
        }
    }

}

jQuery(function() {
    new setCookieNotice(jQuery('#lddw-cookie-modal-box'), {
        expire: window.lddw_cookieslaw.expire,
        domain: window.lddw_cookieslaw.domain
    });
});