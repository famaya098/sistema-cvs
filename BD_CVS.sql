create table admin_users
(
    id                bigint unsigned auto_increment
        primary key,
    name              varchar(255)      not null,
    email             varchar(255)      not null,
    email_verified_at timestamp         null,
    password          varchar(255)      not null,
    nivel_acceso      tinyint default 1 null,
    remember_token    varchar(100)      null,
    created_at        timestamp         null,
    updated_at        timestamp         null,
    constraint email
        unique (email)
)
    collate = utf8mb4_unicode_ci;

create table cache
(
    `key`      varchar(255) not null
        primary key,
    value      mediumtext   not null,
    expiration int          not null
)
    collate = utf8mb4_unicode_ci;

create table cache_locks
(
    `key`      varchar(255) not null
        primary key,
    owner      varchar(255) not null,
    expiration int          not null
)
    collate = utf8mb4_unicode_ci;

create table categorias
(
    id_categoria bigint auto_increment
        primary key,
    nombre       varchar(100)         not null,
    estado       tinyint(1) default 1 null
);

create table estados_academicos
(
    id     bigint auto_increment
        primary key,
    nombre varchar(50)          not null,
    estado tinyint(1) default 1 null
);

create table estados_aplicacion
(
    id          bigint auto_increment
        primary key,
    nombre      varchar(50)          not null,
    estado      tinyint(1) default 1 null,
    Descripcion varchar(255)         null
);

create table failed_jobs
(
    id         bigint unsigned auto_increment
        primary key,
    uuid       varchar(255)                        not null,
    connection text                                not null,
    queue      text                                not null,
    payload    longtext                            not null,
    exception  longtext                            not null,
    failed_at  timestamp default CURRENT_TIMESTAMP not null,
    constraint failed_jobs_uuid_unique
        unique (uuid)
)
    collate = utf8mb4_unicode_ci;

create table job_batches
(
    id             varchar(255) not null
        primary key,
    name           varchar(255) not null,
    total_jobs     int          not null,
    pending_jobs   int          not null,
    failed_jobs    int          not null,
    failed_job_ids longtext     not null,
    options        mediumtext   null,
    cancelled_at   int          null,
    created_at     int          not null,
    finished_at    int          null
)
    collate = utf8mb4_unicode_ci;

create table jobs
(
    id           bigint unsigned auto_increment
        primary key,
    queue        varchar(255)     not null,
    payload      longtext         not null,
    attempts     tinyint unsigned not null,
    reserved_at  int unsigned     null,
    available_at int unsigned     not null,
    created_at   int unsigned     not null
)
    collate = utf8mb4_unicode_ci;

create index jobs_queue_index
    on jobs (queue);

create table migrations
(
    id        int unsigned auto_increment
        primary key,
    migration varchar(255) not null,
    batch     int          not null
)
    collate = utf8mb4_unicode_ci;

create table niveles_academicos
(
    id                    bigint auto_increment
        primary key,
    nombre                varchar(50)          not null,
    estado                tinyint(1) default 1 null,
    requiere_especialidad tinyint(1) default 0 null
);

create table niveles_experiencia
(
    id     bigint auto_increment
        primary key,
    nombre varchar(50)          not null,
    estado tinyint(1) default 1 null
);

create table password_reset_tokens
(
    email      varchar(255) not null
        primary key,
    token      varchar(255) not null,
    created_at timestamp    null
)
    collate = utf8mb4_unicode_ci;

create table secciones
(
    id_seccion bigint auto_increment
        primary key,
    nombre     varchar(100)         not null,
    estado     tinyint(1) default 1 null
);

create table plazas
(
    id_plaza                      bigint auto_increment
        primary key,
    titulo                        varchar(200)                         not null,
    alias_plaza                   varchar(200)                         null,
    id_seccion                    bigint                               null,
    id_categoria                  bigint                               null,
    publicado                     tinyint(1) default 0                 null,
    estado                        tinyint                              not null,
    pagina_principal              tinyint(1) default 0                 null,
    id_autor                      bigint                               null,
    user_id                       bigint unsigned                      null,
    alias_autor                   varchar(50)                          null,
    id_nivel_acceso               bigint                               null,
    created_at                    datetime   default CURRENT_TIMESTAMP null,
    updated_at                    datetime                             null on update CURRENT_TIMESTAMP,
    id_nivel_academico_requerido  bigint                               null,
    id_estado_academico_requerido bigint                               null,
    id_experiencia_requerido      bigint                               null,
    fecha_inicio_publicacion      date                                 null,
    fecha_fin_publicacion         date                                 null,
    contenido_html                longtext                             null,
    accesos                       int        default 0                 null,
    deleted_at                    datetime                             null,
    constraint fk_plazas_estado_academico
        foreign key (id_estado_academico_requerido) references estados_academicos (id)
            on update cascade on delete set null,
    constraint fk_plazas_experiencia
        foreign key (id_experiencia_requerido) references niveles_experiencia (id)
            on update cascade on delete set null,
    constraint fk_plazas_nivel_academico
        foreign key (id_nivel_academico_requerido) references niveles_academicos (id)
            on update cascade on delete set null,
    constraint fk_plazas_users
        foreign key (user_id) references admin_users (id),
    constraint plazas_ibfk_1
        foreign key (id_seccion) references secciones (id_seccion),
    constraint plazas_ibfk_2
        foreign key (id_categoria) references categorias (id_categoria)
);

create index id_autor
    on plazas (id_autor);

create index id_categoria
    on plazas (id_categoria);

create index id_nivel_acceso
    on plazas (id_nivel_acceso);

create index id_seccion
    on plazas (id_seccion);

create index idx_estado_academico_req
    on plazas (id_estado_academico_requerido);

create index idx_experiencia_req
    on plazas (id_experiencia_requerido);

create index idx_nivel_academico_req
    on plazas (id_nivel_academico_requerido);

create index idx_plazas_busqueda
    on plazas (titulo, alias_plaza);

create index idx_plazas_fechas
    on plazas (fecha_inicio_publicacion, fecha_fin_publicacion);

create table sessions
(
    id            varchar(255)    not null
        primary key,
    user_id       bigint unsigned null,
    ip_address    varchar(45)     null,
    user_agent    text            null,
    payload       longtext        not null,
    last_activity int             not null
)
    collate = utf8mb4_unicode_ci;

create index sessions_last_activity_index
    on sessions (last_activity);

create index sessions_user_id_index
    on sessions (user_id);

create table tipos_documentos
(
    id          bigint auto_increment
        primary key,
    nombre      varchar(100)         not null,
    descripcion varchar(255)         null,
    estado      tinyint(1) default 1 null
);

create table documentos
(
    id                bigint auto_increment
        primary key,
    id_tipo_documento bigint                               not null,
    ruta              varchar(255)                         not null,
    nombre_archivo    varchar(255)                         not null,
    estado            tinyint(1) default 1                 null,
    created_at        timestamp  default CURRENT_TIMESTAMP null,
    updated_at        timestamp                            null on update CURRENT_TIMESTAMP,
    constraint fk_documentos_tipos
        foreign key (id_tipo_documento) references tipos_documentos (id)
);

create table users
(
    id                bigint unsigned auto_increment
        primary key,
    name              varchar(255)                           not null,
    email             varchar(255)                           not null,
    role              enum ('aspirante') default 'aspirante' not null,
    id_perfil         bigint                                 null,
    email_verified_at timestamp                              null,
    password          varchar(255)                           not null,
    remember_token    varchar(100)                           null,
    created_at        timestamp                              null,
    updated_at        timestamp                              null,
    constraint users_email_unique
        unique (email)
)
    collate = utf8mb4_unicode_ci;

create table usuario_perfil
(
    id_aspirante        bigint auto_increment
        primary key,
    user_id             bigint unsigned                     null,
    dui_aspirante       varchar(10)                         null,
    nombre_completo     varchar(200)                        not null,
    genero              enum ('M', 'F')                     not null,
    email               varchar(150)                        not null,
    telefono            varchar(20)                         null,
    carrera             varchar(150)                        null,
    especialidad        varchar(150)                        null,
    created_at          timestamp default CURRENT_TIMESTAMP null,
    id_nivel_academico  bigint                              null,
    id_estado_academico bigint                              null,
    id_experiencia      bigint                              null,
    estado              tinyint                             not null,
    updated_at          timestamp                           null,
    deleted_at          timestamp                           null,
    constraint fk_usuario_perfil_estado_academico
        foreign key (id_estado_academico) references estados_academicos (id),
    constraint fk_usuario_perfil_experiencia
        foreign key (id_experiencia) references niveles_experiencia (id),
    constraint fk_usuario_perfil_nivel_academico
        foreign key (id_nivel_academico) references niveles_academicos (id),
    constraint fk_usuario_perfil_users
        foreign key (user_id) references users (id)
);

create table aplicaciones
(
    id_aplicacion        bigint auto_increment
        primary key,
    id_aspirante         bigint                             null,
    id_plaza             bigint                             null,
    fecha_aplicacion     datetime default CURRENT_TIMESTAMP null,
    id_cv                bigint                             null,
    id_documento         bigint                             null,
    id_estado_aplicacion bigint                             null,
    constraint unique_aplicacion
        unique (id_aspirante, id_plaza),
    constraint aplicaciones_ibfk_1
        foreign key (id_aspirante) references usuario_perfil (id_aspirante),
    constraint aplicaciones_ibfk_2
        foreign key (id_plaza) references plazas (id_plaza),
    constraint fk_aplicaciones_documento
        foreign key (id_documento) references documentos (id),
    constraint fk_aplicaciones_estado
        foreign key (id_estado_aplicacion) references estados_aplicacion (id)
);

create index fk_aplicaciones_cv
    on aplicaciones (id_cv);

create index id_estado_aplicacion
    on aplicaciones (id_estado_aplicacion);

create index id_plaza
    on aplicaciones (id_plaza);

create index idx_aplicaciones_fecha
    on aplicaciones (fecha_aplicacion);

create table usuario_documentos
(
    id           bigint auto_increment
        primary key,
    id_aspirante bigint                               not null,
    id_documento bigint                               not null,
    activo       tinyint(1) default 1                 null,
    created_at   timestamp  default CURRENT_TIMESTAMP null,
    updated_at   timestamp                            null on update CURRENT_TIMESTAMP,
    constraint fk_usuario_documentos_aspirante
        foreign key (id_aspirante) references usuario_perfil (id_aspirante),
    constraint fk_usuario_documentos_documento
        foreign key (id_documento) references documentos (id)
);

create index id_estado_academico
    on usuario_perfil (id_estado_academico);

create index id_experiencia
    on usuario_perfil (id_experiencia);

create index id_nivel_academico
    on usuario_perfil (id_nivel_academico);

create index idx_aspirantes_nombre
    on usuario_perfil (nombre_completo);