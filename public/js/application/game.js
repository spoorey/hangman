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
    updateGameInfo();
});

function updateGameInfo() {
    guessedLettersContainer.html('<i class="fa fa-spinner fa-spin"></i>');
    $.get('update', function(data, a, response) {
        processUpdateGameInfoResponse(data, a, response);

    });
}

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
        processUpdateGameInfoResponse(data, a, response);

    });
}

function processUpdateGameInfoResponse(data, a, response) {

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
    if (leftGuesses <= 1) {
        remainingGuessesContainer.text('Du darfst keinen falschen Buchstaben mehr raten!');
        remainingGuessesContainer.addClass('alert alert-danger');
    } else {
        remainingGuessesContainer.text('Du darfst noch ' + (leftGuesses - 1) + ' falsche Buchstaben raten.');
    }


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
        gameLostContainer.show();
        allowedLettersContainer.find('.letter').hide();
        guessInputContainer.hide();
        remainingGuessesContainer.hide();

        imageElement.animate({
            width: '400px'
        }, 2000);
        $('.game-abort').hide();
    } else if (gameWon) {
        gameWonContainer.show();
        guessInputContainer.hide();
        $('.game-abort').hide();
    }

    var imageSrc = '/img/hangman-' + leftGuesses + '.png';
    imageElement.attr('src', imageSrc);

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
