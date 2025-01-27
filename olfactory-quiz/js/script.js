document.addEventListener('DOMContentLoaded', function () {
   
    const questions = Array.from(document.querySelectorAll('.quiz-question'));
    const progressBar = document.getElementById('progress-bar');
    const resultDiv = document.getElementById('quiz-result');
    const blendName = document.getElementById('blend-name');
    const blendDescription = document.getElementById('blend-description');
    const blendIngredients = document.getElementById('blend-ingredients');
    const nextButton = document.getElementById('next-question');

    let currentQuestionIndex = 0;
    const blendCount = {};

    function updateProgress() {
        const progress = ((currentQuestionIndex + 1) / questions.length) * 100;
        progressBar.style.width = progress + '%';
    }

    function showNextQuestion() {
        const selectedOption = questions[currentQuestionIndex].querySelector(
            `input[name="question-${currentQuestionIndex}"]:checked`
        );
        if (!selectedOption) {
            alert('Please select an option before proceeding!');
            return;
        }
    
        const selectedBlend = selectedOption.dataset.blend;
        if (blendCount[selectedBlend]) {
            blendCount[selectedBlend]++;
        } else {
            blendCount[selectedBlend] = 1;
        }
    
        questions[currentQuestionIndex].style.display = 'none';
    
        currentQuestionIndex++;
        if (currentQuestionIndex < questions.length) {
            questions[currentQuestionIndex].style.display = 'block';
            updateProgress();
        } else {
           
            nextButton.textContent = 'Finish';
            nextButton.removeEventListener('click', showNextQuestion);
            nextButton.addEventListener('click', calculateAndShowResult);
            nextButton.style.display = 'block'; 
        }
    }
    

    function calculateAndShowResult() {
        const recommendedBlend = Object.keys(blendCount).reduce((a, b) =>
            blendCount[a] > blendCount[b] ? a : b
        );

        const result = scoringCriteria[recommendedBlend];
        if (result) {
            blendName.textContent = result.name;
            blendDescription.textContent = result.description;
            blendIngredients.textContent = result.ingredients.join(', ');
        }

        resultDiv.style.display = 'block';

        nextButton.style.display = 'none';
    }

    questions.forEach((q, index) => {
        if (index !== 0) q.style.display = 'none'; 
    });
    updateProgress();

    nextButton.addEventListener('click', showNextQuestion);


    console.log(`Current Question Index: ${currentQuestionIndex}`);
console.log(`Next Button Text: ${nextButton.textContent}`);
console.log(`Next Button Display: ${nextButton.style.display}`);

});
