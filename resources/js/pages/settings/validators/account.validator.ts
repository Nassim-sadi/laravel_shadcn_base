import { z } from 'zod'

export const accountValidator = z.object({
  name: z
    .string()
    .min(1, { message: 'Required.' })
    .min(2, { message: 'Name must be at least 2 characters.' })
    .max(30, { message: 'Name must not be longer than 30 characters.' }),
  dob: z
    .string()
    .datetime({ message: 'Please select a valid date.' })
    .optional(),
  language: z
    .string()
    .min(1, { message: 'Please select a language.' }),
})

export type AccountValidator = z.infer<typeof accountValidator>
