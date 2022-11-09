import { ComponentPublicInstance } from 'vue';

export interface ILoading {
    open(): void,
    close(): void
}

export type CustomComponentPublicInstance = ComponentPublicInstance & {
  open(): void,
  close(): void
}
