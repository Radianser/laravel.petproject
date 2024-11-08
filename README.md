## Социальная сеть

Социальная сеть, написанная на Laravel, с использованием Vue и Tailwind CSS.  
Ссылка на сайт: https://fewphrases.ru/

## Какие функции есть
- Регистрация и авторизация с использованием Laravel Breeze
- Возможность добавлять/редактировать/удалять посты/комментарии
- Счетчик количества символов при вводе текста (ограничение 1000 символов)
- Лайки и просмотр лайкнувших (лайки реализованы через вебсокеты с помощью Pusher)
- Ответы на посты других пользователей и просмотр всех ответов у конкретного поста
- Отметка о количестве прошедшего времени у постов/комментариев с момента их публикации
- Отметка о редактировании сообщения
- Возможность ответить на конкретный комментарий
- У картинок и видео можно открыть окно просмотра с комментариями пользователей. Реализовано с помощью Pinia (бывший Vuex)
- Функция пролистывания картинок в окне просмотра (стрелки влево/вправо)
- Определение ссылок в тексте и автоматическая ставка блока с оформленной ссылкой в конце поста
- Копирование и вставка файлов в инпут любыми способами (копирование/перетаскивание файла/текста/ссылки), причем ссылки на фото и видео вставяться как фото и видео
- Парсинг ссылок на такие ресурсы как VK, YouTube, Twitch
- Бесконечная прокрутка постов в ленте (подгрузка постов)
- Возможность загружать аватары и обложки профиля
- Функция определения пользователя в сети, либо времени его последней активности
- Поиск по пользователям
- Галерея и возможность ее просматривать у других пользователей (если у них есть фото)
- Смена языка (ru/en) и темы (светлая/темная)
- Использование Redis для кэширования данных
- Загрузка файлов осуществляется с помощью библиотеки Resumable JS
- Конвертация картинок в формат 'webp' производится с помощью библиотеки Intervention Image

Позже добавлю еще и мессенджер.
