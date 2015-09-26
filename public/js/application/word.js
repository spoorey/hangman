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
            selector: ' .word'
        }
    ];

    var slideUpAnimations = [
        {
            animation: {
                height: 14,
                fontSize: 14
            },
            selector: ' .word'
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

    // moving the cursor out of the list element
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

    // moving the cursor into the word list element
    function moveInAnimation(container) {
        var subject = container.find(slideSubjectSelector);

        if (!container.hasClass('down')) {
            container.addClass('down');
            // do not change the width
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
        var animationSubject;
        if (animation.selector === null) {
            animationSubject = container;
        } else {
            animationSubject = container.find(animation.selector);
        }

        animationSubject.animate(animation.animation, duration);
    });
}