$('#addTopicSubmit').on('click', function () {
    window.location = '?application=forum&controller=' + $('#addTopicForum').val() + '/add'
});
