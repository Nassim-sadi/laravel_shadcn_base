import { z } from 'zod'

export const userStatusSchema = z.enum(['active', 'inactive', 'invited', 'suspended'])
export type UserStatus = z.infer<typeof userStatusSchema>

export const userRoleSchema = z.enum(['super_admin', 'admin', 'user', 'guest'])
export type UserRole = z.infer<typeof userRoleSchema>

export const userSchema = z.object({
  id: z.number(),
  name: z.string(),
  email: z.string(),
  role: userRoleSchema,
  is_active: z.boolean(),
  locale: z.string().optional(),
  avatar: z.string().nullable().optional(),
  created_at: z.string(),
  updated_at: z.string(),
})
export type User = z.infer<typeof userSchema>

export const userListSchema = z.array(userSchema)
