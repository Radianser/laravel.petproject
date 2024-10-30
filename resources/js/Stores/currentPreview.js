import { defineStore } from 'pinia';

// Вы можете называть возвращаемое значение defineStore() как угодно,
// но лучше всего использовать имя хранилища и окружить его `use`
// и `Store` (например, `useUserStore`, `useCartStore`, `useProductStore`)
// первый аргумент - это уникальный id хранилища в вашем приложении.
export const usePreviewStore = defineStore('PreviewPopUp', {
  // остальные параметры...
  state: () => {
    return {
      // для всех этих свойств тип будет определяться автоматически
      id: null,
      type: null,
      src: null,
      user: null,
      created_at: null,
      embedded: null,
      comments: [],
      likes: [],

      items: [],
      index: null,
      weight: 0,
      show: false,
      comment_section_height: null
    }
  },
})