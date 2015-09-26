var guessedLettersContainer;
var wrongLettersContainer;
var allowedLettersContainer;
var gameWonContainer;
var gameLostContainer;
var imageContainer;
var guessInputContainer;
var guessInput;
var invalidLetterContainer;
var remainingGuessesContainer;

$(document).ready(function() {
    // fill out all the variables
    guessedLettersContainer = $('#guessed-letters');
    wrongLettersContainer = $('#wrong-letters');
    allowedLettersContainer = $('#allowed-letters');
    gameWonContainer = $('#game-won');
    gameLostContainer = $('#game-lost');
    guessInputContainer = $('.guess-inputs');
    imageContainer = $('#image-container');
    guessInput = guessInputContainer.find('#letter-input');
    invalidLetterContainer = $('#invalid-letter');
    remainingGuessesContainer = $('#remaining-guesses');
    // fetch the game's status
    updateGameInfo();
});

function updateGameInfo() {
    guessedLettersContainer.html('<i class="fa fa-spinner fa-spin"></i>');
    $.get('update', function(data, a, response) {
        processUpdateGameInfoResponse(data, response);

    });
}

/**
 * Send a request to guess the given letter
 *
 * @param letter
 * @returns {boolean}
 */
function guessLetter(letter) {
    if (letter == '') {
        return false;
    }
    guessInput.attr('disabled', 'disabled');
    guessedLettersContainer.html('<i class="fa fa-spinner fa-spin"></i>');

    $.post(
        'update',
        {
            'letter': letter
        },
        function(data, a, response) {
        processUpdateGameInfoResponse(data, response);

    });
}

/**
 * Process the response of the second tier
 *
 * @param data
 * @param response
 * @returns {boolean}
 */
function processUpdateGameInfoResponse(data, response) {

    // something went horribly wrong
    if (response.status != 200) {
        alert('Fehler: Konnte Spieldaten nicht laden.')
        console.error('Could not load data');
        console.error(data);
        console.error(response);

        return false;
    }

    var letterCount = data.letterCount;
    var guessedLetters = data.guessedLetters;
    var allowedLetters = data.allowedLetters;
    var gameWon = data.gameWon;
    var gameLost = data.gameLost;
    var leftGuesses = data.leftGuesses;

    $('#letter-count').text(letterCount);
    invalidLetterContainer.hide();

    var letters = [];
    var blockedLetters = [];
    wrongLettersContainer.text('');

    // display wrong letters and prepare the array with the correct letters
    guessedLetters.forEach(
        function (letter) {
            blockedLetters.push(letter.letter);
            if (letter.positions == null || letter.positions.length == 0) {
                var htmlLetter = letter.letter;
                if (htmlLetter == ' ') {
                    htmlLetter = '&nbsp;';
                }
                wrongLettersContainer.append('<span class="letter">' + htmlLetter + '</span>');
            }
            letter.positions.forEach(function (position) {
                letters[position] = letter.letter;
            });
        }
    );

    // display how many wrong guesses are left
    if (leftGuesses <= 1) {
        remainingGuessesContainer.text('Du darfst keinen falschen Buchstaben mehr raten!');
        remainingGuessesContainer.addClass('alert alert-danger');
    } else {
        remainingGuessesContainer.text('Du darfst noch ' + (leftGuesses - 1) + ' falsche Buchstaben raten.');
    }

    // display the correctly guessed letters (and emtpy elements for the not-yet-guessed ones
    guessedLettersContainer.text('');
    for (var i = 0; i < letterCount; i ++) {
        if (typeof (letters[i]) === 'undefined') {
            guessedLettersContainer.append(
                '<span class="letter missing-letter">_</span>'
            );
        } else {
            guessedLettersContainer.append(
                '<span class="letter found-letter">'+ letters[i] + '</span>'
            );
        }
    }

    allowedLettersContainer.text('');
    allowedLetters.forEach(function(letter) {
        var allowedLetterElement;
        if ($.inArray(letter, blockedLetters) != -1 || gameWon) {
            allowedLetterElement = $('<span class="letter guessed">' + letter + '</span>');
        } else {
            allowedLetterElement = $('<span class="letter">' + letter + '</span>');
            allowedLetterElement.on('click', function() {
                guessLetter(letter);
            });
        }

        allowedLettersContainer.append(allowedLetterElement);
    });

    var imageElement = imageContainer.find('img');

    if (gameLost) {
        // show game lost info and hide elements that are no longer needed
        gameLostContainer.show();
        allowedLettersContainer.find('.letter').hide();
        guessInputContainer.hide();
        remainingGuessesContainer.hide();

        // animate the image to a bigger size
        imageElement.animate({
            width: '400px'
        }, 2000);
        $('.game-abort').hide();
    } else if (gameWon) {
        // game is won, show/hide stuff accordingly
        gameWonContainer.show();
        guessInputContainer.hide();
        $('.game-abort').hide();
    }

    // get the correct image, according to the left guesses
    var imageSrc = '/img/hangman-' + leftGuesses + '.png';
    imageElement.attr('src', imageSrc);

    // re-enable the guess input, and bind it's event
    guessInput.attr('disabled', null);
    guessInput.val('');
    guessInput.attr('disabled', null);
    guessInput.focus();
    guessInput.on('keyup', function() {
        var guessedLetter = $(this).val().toUpperCase();
        if (guessedLetter !== '') {
            if ($.inArray(guessedLetter, allowedLetters) != -1) {
                guessLetter(guessedLetter);
            } else {
                invalidLetterContainer.css('display', 'inline-block');
            }
        }
        guessInput.val('');
    });
}
