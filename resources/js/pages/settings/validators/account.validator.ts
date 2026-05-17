import { z } from 'zod'

export const accountValidator = z.object({
  name: z.string().min(1, 'Name is required').min(2, 'Name must be at least 2 characters'),
  language: z.string().min(1, 'Please select a language'),
})

export type AccountValidator = z.infer<typeof accountValidator>
