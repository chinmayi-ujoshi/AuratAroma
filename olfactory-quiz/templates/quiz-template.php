<div id="olfactory-quiz-<?php echo $quiz_id; ?>" class="olfactory-quiz">
    <div id="progress-container" style="margin-bottom: 20px; background: #e0e0e0; border-radius: 10px; overflow: hidden; height: 20px;">
        <div id="progress-bar" style="width: 0%; height: 100%; background: #4caf50; transition: width 0.3s;"></div>
    </div>
    <form id="olfactory-quiz-form">
        <?php foreach ($questions as $index => $question): ?>
            <div class="quiz-question" data-question-index="<?php echo $index; ?>">
                <p><?php echo esc_html($question['question']); ?></p>
                <?php foreach ($question['options'] as $option): ?>
                    <label>
                        <input type="radio" 
                               name="question-<?php echo esc_attr($index); ?>" 
                               value="<?php echo esc_attr($option['text']); ?>" 
                               data-blend="<?php echo esc_attr($option['blend']); ?>">
                        <?php echo esc_html($option['text']); ?>
                    </label>
                    <br>
                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>
        <button type="button" id="next-question">Next</button>
    </form>
    <div id="quiz-result" style="display: none;">
        <h3 id="blend-name"></h3>
        <p id="blend-description"></p>
        <p><strong>Ingredients:</strong> <span id="blend-ingredients"></span></p>
    </div>
</div>

<script>
    const scoringCriteria = <?php echo json_encode($scoring); ?>;
</script>
