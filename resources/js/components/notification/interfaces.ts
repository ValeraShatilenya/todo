import { ComponentPublicInstance } from 'vue';

export interface message {
    message: string;
    type?: string;
    duration?: number;
    infinity?: boolean;
}

export interface INotify {
  (message: message): void;
}

export type CustomComponentPublicInstance = ComponentPublicInstance & {
  notify: INotify;
}
