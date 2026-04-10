(function ($) {
    'use strict';

    const selectors = {
        form: '#comment-form',
        listContainer: '#comments-list-container',
        alertBox: '#alert-box',
        sort: '#sort',
        direction: '#direction'
    };

    function getCsrf() {
        return {
            name: $('meta[name="csrf-token-name"]').attr('content'),
            value: $('meta[name="csrf-token-value"]').attr('content')
        };
    }

    function updateCsrfFromResponse(xhr) {
        const tokenName = xhr.getResponseHeader('X-CSRF-TOKEN-NAME');
        const tokenValue = xhr.getResponseHeader('X-CSRF-TOKEN-VALUE');

        if (tokenName && tokenValue) {
            $('meta[name="csrf-token-name"]').attr('content', tokenName);
            $('meta[name="csrf-token-value"]').attr('content', tokenValue);
            $('input[name="' + tokenName + '"]').val(tokenValue);
        }
    }

    function showAlert(message, type = 'success') {
        $(selectors.alertBox).html(
            '<div class="alert alert-' + type + ' alert-dismissible fade show" role="alert">' +
                $('<div>').text(message).html() +
                '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
                    '<span aria-hidden="true">&times;</span>' +
                '</button>' +
            '</div>'
        );
    }

    function clearErrors() {
        $(selectors.form).find('.is-invalid').removeClass('is-invalid');
        $(selectors.form).find('[data-error-for]').text('');
    }

    function renderErrors(errors) {
        clearErrors();

        Object.keys(errors).forEach(function (field) {
            const input = $(selectors.form).find('[name="' + field + '"]');
            const errorBox = $(selectors.form).find('[data-error-for="' + field + '"]');

            input.addClass('is-invalid');
            errorBox.text(errors[field]);
        });
    }

    function validateEmailField() {
        const emailInput = $('#name');
        const value = $.trim(emailInput.val());
        const pattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

        if (!value) {
            emailInput.addClass('is-invalid');
            $('[data-error-for="name"]').text('Поле Email обязательно.');
            return false;
        }

        if (!pattern.test(value)) {
            emailInput.addClass('is-invalid');
            $('[data-error-for="name"]').text('Введите корректный email.');
            return false;
        }

        emailInput.removeClass('is-invalid');
        $('[data-error-for="name"]').text('');
        return true;
    }

    function loadComments(page = 1) {
        const sort = $(selectors.sort).val();
        const direction = $(selectors.direction).val();

        $.ajax({
            url: '/',
            method: 'GET',
            data: {
                page: page,
                sort: sort,
                direction: direction
            },
            success: function (response) {
                if (response.success && response.html) {
                    $(selectors.listContainer).html(response.html);
                }
            },
            error: function () {
                showAlert('Не удалось загрузить комментарии.', 'danger');
            }
        });
    }

    $(document).on('change', selectors.sort + ', ' + selectors.direction, function () {
        loadComments(1);
    });

    $(document).on('click', '.js-page-link', function (e) {
        e.preventDefault();
        const page = $(this).data('page');
        loadComments(page);
    });

    $(document).on('blur', '#name', function () {
        validateEmailField();
    });

    $(document).on('submit', selectors.form, function (e) {
        e.preventDefault();

        clearErrors();

        if (!validateEmailField()) {
            return;
        }

        const form = $(this);
        const formData = form.serializeArray();
        const csrf = getCsrf();

        if (csrf.name && csrf.value && !formData.find(item => item.name === csrf.name)) {
            formData.push({ name: csrf.name, value: csrf.value });
        }

        $.ajax({
            url: form.attr('action'),
            method: 'POST',
            data: $.param(formData),
            success: function (response, textStatus, xhr) {
                updateCsrfFromResponse(xhr);
                if (response.success) {
                    form.trigger('reset');
                    clearErrors();
                    showAlert(response.message, 'success');
                    loadComments(1);
                }
            },
            error: function (xhr) {
                updateCsrfFromResponse(xhr);

                if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                    renderErrors(xhr.responseJSON.errors);
                    return;
                }

                showAlert('Не удалось добавить комментарий.', 'danger');
            }
        });
    });

    $(document).on('click', '.js-delete-comment', function () {
        const id = $(this).data('id');

        if (!window.confirm('Удалить комментарий?')) {
            return;
        }

        const csrf = getCsrf();
        const payload = {};
        if (csrf.name && csrf.value) {
            payload[csrf.name] = csrf.value;
        }

        $.ajax({
            url: '/comments/' + id,
            method: 'POST',
            data: Object.assign({ _method: 'DELETE' }, payload),
            success: function (response, textStatus, xhr) {
                updateCsrfFromResponse(xhr);
                if (response.success) {
                    showAlert(response.message, 'success');
                    loadComments(1);
                }
            },
            error: function (xhr) {
                updateCsrfFromResponse(xhr);
                showAlert('Не удалось удалить комментарий.', 'danger');
            }
        });
    });
})(jQuery);
