// Глобальные функции для работы с сообщениями
function showMessage(type, text) {
    const message = '<div class="alert alert-'+type+' alert-dismissible fade show">'+text+'<button type="button" class="close" data-dismiss="alert">&times;</button></div>';
    $('#message-container').html(message);
    setTimeout(() => $('.alert').alert('close'), 5000);
}

// Функция для получения данных яблока из data-атрибутов
function getAppleDataFromElement(element) {
    const $element = $(element);
    return {
        id: $element.data('id'),
        color: $element.data('color'),
        status: parseInt($element.data('status')) || 0,
        eaten_percent: parseFloat($element.data('eaten-percent')) || 0,
        size: parseFloat($element.data('size')) || 1.0,
        remaining_percent: parseFloat($element.data('remaining-percent')) || 100,
        created_at: $element.data('created-at') || '',
        fallen_at: $element.data('fallen-at') || null
    };
}

// Функция для заполнения модального окна
function fillAppleModal(apple) {
    const modal = $('#appleModal');
    const body = $('#modal-apple-body');
    const title = $('#modal-apple-id');
    
    title.text(apple.id);
    
    // Простой шаблон без использования библиотеки шаблонов
    let html = `
        <div class="apple-modal-content">
            <div class="apple-modal-visual">
                <div class="apple-large apple-${apple.color} ${apple.status === 2 ? 'rotten' : ''}">
                    <div class="apple-body">
                        <div class="apple-highlight"></div>
                        ${apple.status !== 2 ? `
                            <div class="apple-stem"></div>
                            <div class="apple-leaf"></div>
                        ` : ''}
                    </div>
                </div>
            </div>
            
            <div class="apple-modal-info">
                <ul class="list-unstyled">
                    <li><strong>Цвет:</strong> <span class="text-capitalize">${apple.color}</span></li>
                    <li><strong>Статус:</strong> 
                        ${apple.status === 0 ? 'На дереве' : ''}
                        ${apple.status === 1 ? 'На земле' : ''}
                        ${apple.status === 2 ? 'Гнилое' : ''}
                    </li>
                    <li><strong>Создано:</strong> ${apple.created_at}</li>
                    ${apple.fallen_at ? `<li><strong>Упало:</strong> ${apple.fallen_at}</li>` : ''}
                    <li><strong>Размер:</strong> ${parseFloat(apple.size).toFixed(2)}</li>
                </ul>
                
                <div class="apple-eaten-info mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <strong>Съедено:</strong>
                        <span class="badge badge-success" style="font-size: 1.1em; padding: 5px 10px;">
                            ${apple.eaten_percent}%
                        </span>
                    </div>
                    ${apple.eaten_percent > 0 ? `
                    <div class="progress" style="height: 25px;">
                        <div class="progress-bar bg-success" 
                             role="progressbar" 
                             style="width: ${apple.eaten_percent}%"
                             aria-valuenow="${apple.eaten_percent}" 
                             aria-valuemin="0" 
                             aria-valuemax="100">
                            <strong>${apple.eaten_percent}%</strong>
                        </div>
                    </div>
                    ` : `
                    <div class="progress" style="height: 25px;">
                        <div class="progress-bar bg-light text-dark" 
                             role="progressbar" 
                             style="width: 100%"
                             aria-valuenow="0" 
                             aria-valuemin="0" 
                             aria-valuemax="100">
                            <strong>0%</strong>
                        </div>
                    </div>
                    `}
                    <div class="text-center mt-1">
                        <small class="text-muted">Осталось: ${apple.remaining_percent}%</small>
                    </div>
                </div>
            </div>
            
            <div class="apple-modal-actions">
                ${apple.status === 0 ? `
                    <button class="btn btn-warning btn-block apple-fall-btn-modal" data-id="${apple.id}">
                        Уронить яблоко
                    </button>
                ` : ''}
                
                ${apple.status === 1 ? `
                    <div class="mb-3">
                        <button class="btn btn-primary btn-block apple-bite-btn-modal" data-id="${apple.id}">
                            🍎 Откусить (25%)
                        </button>
                    </div>

                ` : ''}
                
                ${apple.status === 2 ? `
                    <div class="alert alert-warning">
                        Это яблоко гнилое и его нельзя есть
                    </div>
                ` : ''}
            </div>
        </div>
    `;
    
    body.html(html);
    
    // Показываем модальное окно
    if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
        // Bootstrap 5 нативный API
        let modalInstance = bootstrap.Modal.getInstance(modal[0]);
        if (!modalInstance) {
            modalInstance = new bootstrap.Modal(modal[0]);
        }
        modalInstance.show();
    } else {
        // jQuery fallback
        modal.modal('show');
    }
}

// Обработка клика на яблоко
$(document).on('click', '.apple-visual', function() {
    const appleElement = $(this);
    const apple = getAppleDataFromElement(appleElement);
    
    // Заполняем модальное окно данными из элемента
    fillAppleModal(apple);
});

// Генерация яблок
$('#generate-btn').click(function() {
    const btn = $(this);
    btn.prop('disabled', true);
    $('#generate-spinner').removeClass('d-none');
    
    $.ajax({
        url: window.appleUrls.generate,
        type: 'POST',
        data: {
            '_csrf': yii.getCsrfToken()
        },
        success: function(response) {
            if (response.success) {
                // Добавляем новое яблоко в соответствующую зону
                const appleHtml = response.html;
                const $appleHtml = $(appleHtml);
                const appleStatus = $appleHtml.data('status');
                
                if (appleStatus === 0) {
                    // Яблоко на дереве
                    if ($('#apples-on-tree .no-apples').length) {
                        $('#apples-on-tree').html(appleHtml);
                    } else {
                        $('#apples-on-tree').append(appleHtml);
                    }
                } else {
                    // Яблоко на земле
                    if ($('#apples-on-ground .no-apples').length) {
                        $('#apples-on-ground').html(appleHtml);
                    } else {
                        $('#apples-on-ground').append(appleHtml);
                    }
                }
                
                const currentCount = parseInt($('#apple-count').text()) || 0;
                $('#apple-count').text(currentCount + response.count);
                showMessage('success', response.message);
            } else {
                showMessage('danger', response.message);
            }
        },
        error: function() {
            showMessage('danger', 'Ошибка при генерации яблок');
        },
        complete: function() {
            btn.prop('disabled', false);
            $('#generate-spinner').addClass('d-none');
        }
    });
});

// Обработка кнопки "Уронить" в модальном окне
$(document).on('click', '.apple-fall-btn-modal', function() {
    const appleId = $(this).data('id');
    const btn = $(this);
    
    btn.prop('disabled', true);
    btn.text('Обработка...');
    
    $.ajax({
        url: '/apple/fall?id='+appleId,
        type: 'POST',
        data: {
            '_csrf': yii.getCsrfToken()
        },
        success: function(response) {
            if (response.success) {
                const modal = $('#appleModal');
                if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                    const modalInstance = bootstrap.Modal.getInstance(modal[0]);
                    if (modalInstance) {
                        modalInstance.hide();
                    } else {
                        modal.modal('hide');
                    }
                } else {
                    modal.modal('hide');
                }
                
                // Перемещаем яблоко из дерева на землю
                const appleElement = $(`.apple-visual[data-id="${appleId}"]`);
                
                // Удаляем из дерева
                appleElement.detach();
                
                // Обновляем data-атрибуты
                appleElement.attr('data-status', '1');
                appleElement.removeClass('on-tree').addClass('on-ground');
                if (response.fallen_at) {
                    appleElement.attr('data-fallen-at', new Date(response.fallen_at).toLocaleString('ru-RU', {
                        year: 'numeric',
                        month: '2-digit',
                        day: '2-digit',
                        hour: '2-digit',
                        minute: '2-digit'
                    }));
                }
                
                // Обновляем визуальное отображение - убираем стебель и лист для упавших яблок
                appleElement.find('.apple-stem').remove();
                appleElement.find('.apple-leaf').remove();
                
                // Добавляем на землю
                if ($('#apples-on-ground .no-apples').length) {
                    $('#apples-on-ground').html(appleElement);
                } else {
                    $('#apples-on-ground').append(appleElement);
                }
                location.reload();
                
            } else {
                showMessage('danger', response.message);
                btn.prop('disabled', false);
                btn.text('Уронить яблоко');
            }
        },
        error: function() {
            showMessage('danger', 'Ошибка при падении яблока');
            btn.prop('disabled', false);
            btn.text('Уронить яблоко');
        }
    });
});

// Обработка кнопки "Откусить" в модальном окне
$(document).on('click', '.apple-bite-btn-modal', function() {
    const appleId = $(this).data('id');
    const btn = $(this);
    const appleElement = $(`.apple-visual[data-id="${appleId}"]`);
    const remainingPercent = parseFloat(appleElement.data('remaining-percent')) || 100;
    const bitePercent = Math.min(25, remainingPercent);
    
    if (bitePercent <= 0) {
        showMessage('warning', 'Яблоко уже полностью съедено!');
        return;
    }
    
    btn.prop('disabled', true);
    btn.text('Обработка...');
    
    $.ajax({
        url: '/apple/eat?id='+appleId,
        type: 'POST',
        data: {
            '_csrf': yii.getCsrfToken(),
            'percent': bitePercent
        },
        success: function(response) {
            if (response.success) {
                if (response.removed) {
                    // Яблоко полностью съедено
                    const modal = $('#appleModal');
                    const modalInstance = bootstrap.Modal.getInstance(modal[0]);
                    if (modalInstance) {
                        modalInstance.hide();
                    } else {
                        modal.modal('hide');
                    }
                    
                    appleElement.fadeOut(500, function() {
                        $(this).remove();
                        const currentCount = parseInt($('#apple-count').text()) || 0;
                        $('#apple-count').text(Math.max(0, currentCount - 1));
                    });
                    
                    showMessage('success', response.message);
                } else {
                    // Яблоко частично съедено - обновляем данные из ответа
                    if (response.eaten_percent !== undefined) {
                        appleElement.attr('data-eaten-percent', response.eaten_percent);
                    }
                    if (response.remaining_percent !== undefined) {
                        appleElement.attr('data-remaining-percent', response.remaining_percent);
                    }
                    if (response.status !== undefined) {
                        appleElement.attr('data-status', response.status);
                    }
                    
                    // Получаем обновленные данные и заполняем модальное окно
                    const apple = getAppleDataFromElement(appleElement);
                    console.log(apple);
                    fillAppleModal(apple);
                    
                    // Обновляем визуальное отображение яблока
                    if (apple.eaten_percent > 0) {
                        appleElement.addClass('partially-eaten');
                        let indicator = appleElement.find('.apple-eaten-indicator');
                        if (indicator.length === 0) {
                            appleElement.append('<div class="apple-eaten-indicator"></div>');
                            indicator = appleElement.find('.apple-eaten-indicator');
                        }
                        indicator.css('width', apple.eaten_percent + '%');
                        
                        // Обновляем текст процента
                        let percentText = appleElement.find('.apple-eaten-percent-text');
                        if (percentText.length === 0) {
                            appleElement.append(`<div class="apple-eaten-percent-text">${apple.eaten_percent}%</div>`);
                        } else {
                            percentText.text(apple.eaten_percent + '%');
                        }
                    } else {
                        // Удаляем индикаторы если яблоко не съедено
                        appleElement.find('.apple-eaten-indicator').remove();
                        appleElement.find('.apple-eaten-percent-text').remove();
                        appleElement.removeClass('partially-eaten');
                    }
                    
                    // Обновляем визуализацию если яблоко стало гнилым
                    if (apple.status === 2) {
                        appleElement.addClass('rotten').removeClass('on-ground');
                        appleElement.find('.apple-stem').remove();
                        appleElement.find('.apple-leaf').remove();
                    }
                    
                    location.reload();
                }
            } else {
                showMessage('danger', response.message);
                btn.prop('disabled', false);
                btn.text('🍎 Откусить (25%)');
            }
        },
        error: function() {
            showMessage('danger', 'Ошибка при откусывании яблока');
            btn.prop('disabled', false);
            btn.text('🍎 Откусить (25%)');
        }
    });
});

// Обработка формы "Съесть" в модальном окне
$(document).on('submit', '.apple-eat-form-modal', function(e) {
    e.preventDefault();
    const form = $(this);
    const appleId = form.data('id');
    const btn = form.find('button[type="submit"]');
    
    btn.prop('disabled', true);
    btn.text('Обработка...');
    
    $.ajax({
        url: '/apple/eat?id='+appleId,
        type: 'POST',
        data: form.serialize(),
        success: function(response) {
            if (response.success) {
                if (response.removed) {
                    // Яблоко полностью съедено
                    const modal = $('#appleModal');
                    const modalInstance = bootstrap.Modal.getInstance(modal[0]);
                    if (modalInstance) {
                        modalInstance.hide();
                    } else {
                        modal.modal('hide');
                    }
                    
                    const appleElement = $(`.apple-visual[data-id="${appleId}"]`);
                    appleElement.fadeOut(500, function() {
                        $(this).remove();
                        const currentCount = parseInt($('#apple-count').text()) || 0;
                        $('#apple-count').text(Math.max(0, currentCount - 1));
                    });
                    
                    showMessage('success', response.message);
                } else {
                    // Яблоко частично съедено - обновляем данные из ответа
                    const appleElement = $(`.apple-visual[data-id="${appleId}"]`);
                    
                    // Обновляем data-атрибуты из ответа
                    if (response.eaten_percent !== undefined) {
                        appleElement.attr('data-eaten-percent', response.eaten_percent);
                    }
                    if (response.remaining_percent !== undefined) {
                        appleElement.attr('data-remaining-percent', response.remaining_percent);
                    }
                    if (response.status !== undefined) {
                        appleElement.attr('data-status', response.status);
                    }
                    
                    // Получаем обновленные данные и заполняем модальное окно
                    const apple = getAppleDataFromElement(appleElement);
                    fillAppleModal(apple);
                    
                    // Обновляем визуальное отображение яблока
                    if (apple.eaten_percent > 0) {
                        appleElement.addClass('partially-eaten');
                        let indicator = appleElement.find('.apple-eaten-indicator');
                        if (indicator.length === 0) {
                            appleElement.append('<div class="apple-eaten-indicator"></div>');
                            indicator = appleElement.find('.apple-eaten-indicator');
                        }
                        indicator.css('width', apple.eaten_percent + '%');
                        
                        // Обновляем текст процента
                        let percentText = appleElement.find('.apple-eaten-percent-text');
                        if (percentText.length === 0) {
                            appleElement.append(`<div class="apple-eaten-percent-text">${apple.eaten_percent}%</div>`);
                        } else {
                            percentText.text(apple.eaten_percent + '%');
                        }
                    } else {
                        // Удаляем индикаторы если яблоко не съедено
                        appleElement.find('.apple-eaten-indicator').remove();
                        appleElement.find('.apple-eaten-percent-text').remove();
                        appleElement.removeClass('partially-eaten');
                    }
                    
                    // Обновляем визуализацию если яблоко стало гнилым
                    if (apple.status === 2) {
                        appleElement.addClass('rotten').removeClass('on-ground');
                        appleElement.find('.apple-stem').remove();
                        appleElement.find('.apple-leaf').remove();
                    }
                    
                    showMessage('success', response.message);
                }
            } else {
                showMessage('danger', response.message);
                btn.prop('disabled', false);
                btn.text('🍽️ Съесть');
            }
        },
        error: function() {
            showMessage('danger', 'Ошибка при поедании яблока');
            btn.prop('disabled', false);
            btn.text('🍽️ Съесть');
        }
    });
});

// Проверка гниения (если кнопка существует)
$('#check-rotting-btn').click(function() {
    const btn = $(this);
    btn.prop('disabled', true);
    
    // Здесь можно добавить AJAX запрос для проверки гниения всех яблок
    // Пока просто обновляем страницу
    location.reload();
});

// Обработчик закрытия модального окна через кнопку (для надежности)
$(document).on('click', '#appleModal .btn-close', function() {
    const modal = $('#appleModal');
    if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
        const modalInstance = bootstrap.Modal.getInstance(modal[0]);
        if (modalInstance) {
            modalInstance.hide();
        } else {
            modal.modal('hide');
        }
    } else {
        modal.modal('hide');
    }
});
