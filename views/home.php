<div class="card">
    <div class="card-header d-flex align-items-center justify-content-between">
        <h5 class="mb-0">Polls(<?= count($getMyPolls) ?>)</h5>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#myModal">
            Create poll
        </button>
    </div>
    <div class="card-body">
        <table class="table">
            <thead>
                <tr>
                    <th scope="col">Title</th>
                    <th scope="col">Question</th>
                    <th scope="col">Status</th>
                    <th scope="col">Created at</th>
                    <th scope="col">Action</th>
                </tr>
            </thead>
            <tbody>

                <?php
                foreach ($getMyPolls as $poll) {

                ?>
                    <tr data-id="<?= $poll['id'] ?>">
                        <td><?= $poll['title'] ?></td>
                        <td><?= $poll['question'] ?></td>
                        <td><?= $poll['is_active'] ? '<a class="btn btn-primary">Published</a>' : '<a class="btn btn-danger">Draft</a>' ?></td>
                        <td><?= $poll['created_at'] ?></td>
                        <td>
                           
                            <button type="button" data-bs-toggle="modal" name="update_button" data-poll-id="<?= $poll['id'] ?>" data-bs-target="#modal_update" class="btn btn-success"><i class="fas fa-edit"></i></button>
                            <form name="form" data-action="destroy" data-id="<?= $poll['id'] ?>" action="<?= $this->getUrl('api/poll-destroy') ?>" method="post">
                                <input type="hidden" name="poll_id" value="<?= $poll['id'] ?>">
                                <button type="submit" class="btn btn-danger"><i class="far fa-trash-alt"></i></button>
                            </form>
                        </td>
                    </tr>
                <?php
                }
                ?>

            </tbody>
        </table>
    </div>

</div>
<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" aria-labelledby="store_poll" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="store_poll">Store poll</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= $this->getUrl('/api/poll-store') ?>" data-action="store" name="form" method="post">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="title">Title:</label>
                        <input type="text" class="form-control" name="title" id="title">
                    </div>
                    <div class="form-group">
                        <label for="question">Question:</label>
                        <textarea class="form-control" name="question" id="question"></textarea>
                    </div>
                    <div class="form-group form-check">
                        <input type="checkbox" class="form-check-input" name="is_active" value="1" id="is_active">
                        <label class="form-check-label" for="is_active">Status</label>
                    </div>
                    <div class="form-group" id="answers">
                        <label for="answer">Answers:</label>
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" name="answers[0][answer]">
                            <input type="number" class="form-control" name="answers[0][votes]" placeholder="Votes">
                            <button class="btn btn-outline-secondary" id="add-answer" type="button" >Add Answer</button>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>
                    <button type="submit" class="btn btn-primary">Сохранить изменения</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal update -->

<div class="modal fade" id="modal_update" tabindex="-1" aria-labelledby="update_poll" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="update_poll">Update poll </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= $this->getUrl('/api/poll-update') ?>" data-action="update" name="form" method="post">
                <div class="modal-body">
                    <input type="hidden" name="poll_id" id="poll_id">
                    <div class="form-group">
                        <label for="title">Title:</label>
                        <input type="text" class="form-control" name="title" id="title_update">
                    </div>
                    <div class="form-group">
                        <label for="question">Question:</label>
                        <textarea class="form-control" name="question" id="question_update"></textarea>
                    </div>
                    <div class="form-group form-check">
                        <input type="checkbox" class="form-check-input" name="is_active" value="1" id="is_active_update">
                        <label class="form-check-label" for="is_active">Status</label>
                    </div>
                    <div class="form-group" id="answers_update">
                        <label for="answer">Answers:</label>
                        
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>
                    <button type="submit" class="btn btn-primary">Сохранить изменения</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        let count = 0;
        $("body").on('click', '#add-answer', function() {
            count++;
            var html = `<div class="input-group mb-3">
                            <input type="text" class="form-control" name="answers[${count}][answer]">
                            <input type="number" class="form-control" name="answers[${count}][votes]" placeholder="Votes">
                            <button class="btn btn-danger remove-answer" type="button" id="add-answer">Remove</button>
                        </div>`;
            $("#answers").append(html);
        });

        $(document).on('click', '.remove-answer', function() {
            const id = $(this).parent().attr('data-answer-id');
            if(id > 0){
                $.ajax({
                url: '<?= $this->getUrl('/api/poll-destroy-answer') ?>',
                method: 'get',
                dataType: 'json',
                data: `?answer_id=${id}`,
                success: function(data){
                    
                }
            });
            }
            $(this).parent().remove();
        });


        let i = 0;
        $("body").on('click', 'button[name=update_button]', function(){
            let id = $(this).attr('data-poll-id');
            $.ajax({
                url: '<?= $this->getUrl('/api/poll-update-get') ?>',
                method: 'get',
                dataType: 'json',
                data: `?poll_id=${id}`,
                success: function(data){
                    console.log(data);
                    $("#title_update").val(data.data.title);
                    $("#question_update").val(data.data.question);
                    if(data.data.is_active == 1){
                        $("#is_active_update").attr('checked', 'checked');
                    } else {
                        $("#is_active_update").removeAttr('checked');
                    }
  
                    $("#answers_update").html('<label for="answer">Answers:</label>');
                    $("#poll_id").val(id);
                    data.answers.forEach((element) => {
           
                        let append = `
                        <div class="input-group mb-3">
                            <input type="text" value="${element.answer}" class="form-control" name="answers[${element.id}][answer]">
                            <input type="number" value="${element.votes}"  class="form-control" name="answers[${element.id}][votes]" placeholder="Votes">
                            <button class="btn btn-outline-secondary add-answer" type="button">Add Answer</button>
                        </div>`;
                        
                        if(i != 0){
                           append = `
                            <div class="input-group mb-3" data-answer-id="${element.id}">
                                <input type="text" value="${element.answer}" class="form-control" name="answers[${element.id}][answer]">
                                <input type="number" value="${element.votes}" class="form-control" name="answers[${element.id}][votes]" placeholder="Votes">
                                <button class="btn btn-danger remove-answer" type="button" >Remove</button>
                            </div>`;
                        }
                        $("#answers_update").append(append);
                        i++;
                    });
                }
            });
        });
        $("body").on('click', '.add-answer', function() {
            
            var html = `<div class="input-group mb-3">
                            <input type="text" class="form-control" name="answers[${i}][answer]">
                            <input type="number" class="form-control" name="answers[${i}][votes]" placeholder="Votes">
                            <button class="btn btn-danger remove-answer" type="button" id="add-answer">Remove</button>
                        </div>`;
            $("#answers_update").append(html);
            i++;
        });
    });
</script>