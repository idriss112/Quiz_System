let quizId = QUIZ_ID;
let current = 0;
let score = 0;

async function loadQuestion() {
    const res = await fetch(`ajax/load_question.php?quiz_id=${quizId}&index=${current}`);
    const data = await res.json();

    if (!data || !data.question) {
        document.getElementById('quizBox').style.display = 'none';
        document.getElementById('result').style.display = 'block';
        document.getElementById('result').innerText = `Votre score est ${score}`;
        fetch('ajax/save_result.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `quiz_id=${quizId}&score=${score}`
        });
        return;
    }

    document.getElementById('quizBox').classList.add('active');
    document.getElementById('questionText').innerText = data.question;
    document.getElementById('answersContainer').innerHTML = '';

    const options = [...data.options];
    for (let opt of options) {
        document.getElementById('answersContainer').innerHTML += `
      <label><input type="radio" name="answer" value="${opt}" required> ${opt}</label>
    `;
    }
}

async function submitAnswer() {
    const selected = document.querySelector('input[name="answer"]:checked');
    if (!selected) {
        alert("Veuillez choisir une réponse");
        return;
    }

    const res = await fetch('ajax/submit_answer.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `quiz_id=${quizId}&index=${current}&answer=${selected.value}`
    });
    const data = await res.json();

    if (data.correct) score++;
    current++;
    loadQuestion();
}

document.addEventListener("DOMContentLoaded", loadQuestion);



finishQuiz();