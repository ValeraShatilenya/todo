import { ComponentPublicInstance } from 'vue';

export interface confirmData {
    title?: string;
    message: string;
    width?: number;
    confirmText?: string;
    confirmColorClass?: string;
    cancelText?: string;
    onConfirm?(): any;
}

export interface IConfirm {
  (confirmData: confirmData): void;
}

export type CustomComponentPublicInstance = ComponentPublicInstance & {
    open: IConfirm;
}
