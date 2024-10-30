import { defineStore } from 'pinia';

// Вы можете называть возвращаемое значение defineStore() как угодно,
// но лучше всего использовать имя хранилища и окружить его `use`
// и `Store` (например, `useUserStore`, `useCartStore`, `useProductStore`)
// первый аргумент - это уникальный id хранилища в вашем приложении.
export const useSessionStore = defineStore('PreviewPopUp', {
  // остальные параметры...
  state: () => {
    return {
      // для всех этих свойств тип будет определяться автоматически
      theme: null,
      language: null,
      sorting: null
    }
  },
})