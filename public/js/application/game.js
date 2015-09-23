var guessedLettersContainer;
var wrongLettersContainer;
$(document).ready(function() {
    guessedLettersContainer = $('#guessed-letters');
    wrongLettersContainer = $('#wrong-letters');
    updateGameInfo();
});

function updateGameInfo() {
    guessedLettersContainer.html('<i class="fa fa-spinner fa-spin"></i>');
    $.get('update', function(data, a, response) {
        processUpdateGameInfoResponse(data, a, response);

    });
}

function guessLetter(letter) {
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

    $('#letter-count').text(letterCount);

    var letters = [];
    wrongLettersContainer.text('');
    guessedLetters.forEach(
        function (letter) {
            if (letter.positions == null || letter.positions.length == 0) {
                wrongLettersContainer.append('<span class="letter">' + letter.letter + '</span>');
            }
            letter.positions.forEach(function (position) {
                letters[position] = letter.letter;
            });
        }
    );

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
}
