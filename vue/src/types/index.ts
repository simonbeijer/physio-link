export interface Exercise {
    id: number,
    name: string,
    youtube_url: string,
    start_time: number,
    end_time: number,
    timer_duration: number,
}

export interface Schema {
    id: number,
    name: string,
    share_token: string,
}

export interface SchemaExercise {
    schema_id: number,
    exercise_id: number,
    order: number,
    comfort_level: number | null,
    exercise: Exercise,
}