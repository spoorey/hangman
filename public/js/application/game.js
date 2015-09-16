$(document).ready(function() {
    updateGameInfo();
});
function updateGameInfo() {
    $.get('update', function(data, a, response) {
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
        guessedLetters.forEach(
            function (letter) {

                letter.positions.forEach(function (position) {
                    letters[position] = letter.letter;
                });
            }
        );

        var guessedLettersContainer = $('#guessed-letters');
        guessedLettersContainer.text('');
        for (var i = 0; i < letterCount; i ++) {
            if (typeof (letters[i]) === 'undefined') {
                guessedLettersContainer.append(
                    '<span class="letter missing-letter"></span>'
                );
            } else {
                guessedLettersContainer.append(
                    '<span class="letter found-letter">'+ letters[i] + '</span>'
                );
            }
        }
    });
}
