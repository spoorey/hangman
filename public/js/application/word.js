/**
 * Created by David Spörri on 19.08.2015.
 */
$(document).ready(function () {
    var list = $('.word-list-container');
    var slideSubjectSelector = '.slide-down';
    var word = $('.word');
    var slideDownAnimations = [
        {
            animation: {
                height: word.css('height'),
                fontSize: word.css('font-size')
            },

            forceNoChange: [
            ],
            selector: ' .word'
        },
        {
            animation: {
            },
            forceNoChange: [
            ],
            selector: null
        }
    ];

    var slideUpAnimations = [
        {
            animation: {
                height: 14,
                fontSize: 14
            },
            forceNoChange: [
            ],
            selector: ' .word'
        },
        {
            animation: {
            },
            forceNoChange: [
            ],
            selector: null
        }
    ];

    var slideAnimationDuration = 500;

    list.find('.entry').hover(
        function() {
            //$('.entry').finish();
            moveInAnimation($(this));
        },
        function() {
            moveOutAnimation($(this));
        }
    );

    function moveOutAnimation(container) {
        var subject = container.find(slideSubjectSelector);
        var _this = container;
        processAnimations(slideDownAnimations, slideAnimationDuration, _this);


        if (container.is(':hover')) {
            moveInAnimation(container);
            return;
        }

        subject.slideUp(slideAnimationDuration, function() {
            _this.removeClass('down');
            container.removeClass('down');
            if (container.is(':hover')) {
                moveInAnimation(container);
            }
        });
    }

    function moveInAnimation(container) {
        var subject = container.find(slideSubjectSelector);

        if (!container.hasClass('down')) {
            container.addClass('down');
            container.css('width', container.width());

            processAnimations(
                slideUpAnimations,
                slideAnimationDuration,
                container
            );
            subject.slideDown(slideAnimationDuration, function() {
            });
        }
    }
});

function processAnimations(animations, duration, container) {
    animations.forEach(function (animation) {
        var beforeValues = [];

        var animationSubject;
        if (animation.selector === null) {
            animationSubject = container;
        } else {
            animationSubject = container.find(animation.selector);
        }

        animation.forceNoChange.forEach(function (attribute) {
            var cssValue = animationSubject.css(attribute);


            if (cssValue != null) {
                beforeValues[attribute] = cssValue;
                console.log(attribute, cssValue);
            } else {
                beforeValues[attribute] = animationSubject.attr(attribute);
            }
        });

        animationSubject.animate(animation.animation, duration);

        animation.forceNoChange.forEach(function (attribute) {
            if (attribute == 'width') {
                animationSubject.css('width', beforeValues['width']);
                return;
            }



            animationSubject.attr(attribute, beforeValues[attribute]);
            animationSubject.css(attribute, beforeValues[attribute]);
        });

    });
}