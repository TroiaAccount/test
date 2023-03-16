$("body").on('submit', 'form[name=form]', function (e) {
    e.preventDefault();
    let form = $(this);
    $.ajax({
        url: form.attr('action'),
        method: 'post',
        dataType: 'json',
        data: form.serialize(),
        success: function (data) {
            if (data.status == true) {
                toastr.success(data.data);
                if (form.attr('data-action') == "login") {
                    location = "/";
                }

                const searchParams = new URLSearchParams(form.serialize());
                const params = {};
                for (const [key, value] of searchParams.entries()) {
                    params[key] = value;
                }

                const date = new Date();

                // Получаем значения года, месяца, дня, часов, минут и секунд
                let year = date.getFullYear();
                let month = date.getMonth() + 1;
                let day = date.getDate();
                let hours = date.getHours();
                let minutes = date.getMinutes();
                let seconds = date.getSeconds();

                // Форматируем значения в нужный формат
                if (month < 10) { month = "0" + month; }
                if (day < 10) { day = "0" + day; }
                if (hours < 10) { hours = "0" + hours; }
                if (minutes < 10) { minutes = "0" + minutes; }
                if (seconds < 10) { seconds = "0" + seconds; }

                // Соединяем значения в строку в нужном формате
                const formattedDate = year + "-" + month + "-" + day + " " + hours + ":" + minutes + ":" + seconds;


                switch (form.attr('data-action')) {
                    case "login":
                        location = "/"
                        break;
                    case "destroy":
                        $("tr[data-id=" + form.attr('data-id') + "]").remove();
                        break;
                    case "store":
                        let button = `<a class="btn btn-primary">Published</a>`;
                        if (params['is_active'] != 1) {
                            button = `<a class="btn btn-danger">Draft</a>`;
                        }
                        $("tbody").append(
                            `<tr data-id="${data.id}">
                                <td>${params['title']}</td>
                                <td>${params['question']}</td>
                                <td>${button}</td>
                                <td>${formattedDate}</td>
                                <td>
                                   
                                    <button type="button" data-bs-toggle="modal" name="update_button" data-poll-id="${data.id}" data-bs-target="#modal_update" class="btn btn-success"><i class="fas fa-edit"></i></button>
                                    <form name="form" data-action="destroy" data-id="${data.id}" action="${data.destroy_url}" method="post">
                                        <input type="hidden" name="poll_id" value="${data.id}">
                                        <button type="submit" class="btn btn-danger"><i class="far fa-trash-alt"></i></button>
                                    </form>
                                </td>
                            </tr>`
                        );
                        break;
                    case "update":
                        location.reload();
                    break;
                }
            } else {
                if (typeof data.errors == "string") {
                    toastr.error(data.errors);
                } else {
                    for (let key in data.errors) {
                        toastr.error(data.errors[key]);
                    }
                }
            }
        }
    });
});