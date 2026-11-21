@extends('layouts.site')

@section('title', $page->title.' · '.$site->name)
@section('meta_description', $page->meta_description ?? $site->description)

@php
    $isCaudeteAboutPage = $site->slug === 'caudete-se-mueve'
        && $page->slug === 'quienes-somos'
        && trim((string) $page->body) === "Somos una asociación vecinal abierta a todo el barrio.\n\nExistimos para conocer los problemas concretos del entorno y para abrir canales de participación real, no solo consultiva.";

    $isCaudeteWorkPage = $site->slug === 'caudete-se-mueve'
        && $page->slug === 'que-hacemos';

    $isCaudeteDonationsPage = $site->slug === 'caudete-se-mueve'
        && in_array($page->slug, ['colabora', 'donaciones'], true);

    $workAreas = [
        [
            'number' => '01',
            'title' => 'Ayuda Humanitaria y Cooperación Internacional',
            'intro' => 'Llevamos la solidaridad de nuestro municipio a los lugares más golpeados por las crisis humanitarias y los conflictos bélicos.',
            'image' => 'que-hacemos/caudete-carga-ayuda-humanitaria.jpg',
            'alt' => 'Voluntariado carga material de ayuda humanitaria en un camión.',
            'caption' => 'Preparación y envío de ayuda humanitaria.',
            'activities' => [
                ['title' => 'Gestión y envío de contenedores:', 'description' => 'Centralizamos la recogida, clasificación, triaje y paletizado técnico de material de primera necesidad (ropa técnica, calzado, productos de higiene) para su envío a campos de personas refugiadas en Europa.'],
                ['title' => 'Apoyo a personas en tránsito:', 'description' => 'Colaboramos en redes de soporte logístico de emergencia y campañas de ropa de abrigo orientadas a dar auxilio a las personas migrantes en las fronteras y rutas migratorias europeas.'],
                ['title' => 'Logística de refugio de emergencia:', 'description' => 'Coordinamos el aprovisionamiento de tiendas de campaña, mantas y material de cobijo para asentamientos vulnerables y puntos calientes de asistencia humanitaria.'],
            ],
        ],
        [
            'number' => '02',
            'title' => 'Acción Social y Red de Distribución Local',
            'intro' => 'Ninguna crisis nos es ajena. Trabajamos estrechamente con el tejido asociativo de nuestro municipio para mitigar la vulnerabilidad en nuestro entorno más cercano.',
            'image' => 'que-hacemos/caudete-grupo-con-donacion.jpg',
            'alt' => 'Vecinas y voluntariado junto a una donación recogida en Caudete.',
            'caption' => 'Redes vecinales de recogida y apoyo local.',
            'activities' => [
                ['title' => 'Logística de alimentos de urgencia:', 'description' => 'Recepcionamos y gestionamos partidas de alimentos, priorizando los artículos con fechas de caducidad temprana para activar redes de reparto rápido e inmediato hacia las familias de Caudete que más lo necesitan.'],
                ['title' => 'Aprovechamiento de excedentes:', 'description' => 'Coordinamos la recogida y distribución de excedentes agrícolas e industriales de empresas locales para su canalización urgente a través de entidades benéficas del municipio, evitando el desperdicio y garantizando el derecho a la alimentación.'],
            ],
        ],
        [
            'number' => '03',
            'title' => 'Movilidad Solidaria y Economía Circular',
            'intro' => 'Creemos en una solidaridad sostenible que proteja el medioambiente a la vez que dota de autonomía a las personas.',
            'placeholder' => 'bicycle',
            'placeholder_label' => 'Fotografía pendiente: taller solidario de bicicletas.',
            'placeholder_caption' => 'Taller solidario de bicicletas',
            'activities' => [
                ['title' => 'Taller solidario de bicicletas:', 'description' => 'Recogemos bicicletas donadas por la ciudadanía y colectivos de la comarca para proceder a su completa reparación técnica y puesta a punto.'],
                ['title' => 'Autonomía para colectivos vulnerables:', 'description' => 'Entregamos estas bicicletas a trabajadores temporeros, personas migrantes en periodos formativos y colectivos vulnerables de la provincia, transformando el reciclaje material en una herramienta directa de transporte digno, inserción y libertad de movimiento.'],
            ],
        ],
        [
            'number' => '04',
            'title' => 'Educación en Valores y Sensibilización Comunitaria',
            'intro' => 'Para construir un futuro sin injusticias, es imprescindible formar y conmover a las generaciones del presente.',
            'placeholder' => 'education',
            'placeholder_label' => 'Fotografía pendiente: jornada educativa o taller escolar.',
            'placeholder_caption' => 'Jornada educativa o taller escolar',
            'activities' => [
                ['title' => 'Jornadas y talleres escolares:', 'description' => 'Entramos en las aulas de los centros educativos de Caudete para realizar dinámicas pedagógicas, charlas de concienciación y recreaciones didácticas orientadas a acercar la realidad de los campos de refugiados a los más jóvenes.'],
                ['title' => 'Implicación de la comunidad educativa:', 'description' => 'Involucramos activamente al alumnado, profesorado y familias en campañas de recogida escolar y en la preparación de la logística de los envíos humanitarios.'],
                ['title' => 'Formación de educadores:', 'description' => 'Participamos en cursos formativos locales para transmitir los valores del asociacionismo formal, el voluntariado comunitario y la educación en valores a los futuros profesionales del ocio y el tiempo libre.'],
            ],
        ],
        [
            'number' => '05',
            'title' => 'Movilizaciones por la Paz y los Derechos Humanos',
            'intro' => 'La asistencia material no es suficiente si no señalamos las causas de la injusticia. Combinamos la ayuda humanitaria con el activismo político y social.',
            'placeholder' => 'peace',
            'placeholder_label' => 'Fotografía pendiente: concentración o movilización ciudadana por la paz.',
            'placeholder_caption' => 'Concentración ciudadana por la paz',
            'activities' => [
                ['title' => 'Concentraciones ciudadanas:', 'description' => 'Convocamos movilizaciones públicas y pacíficas de manera regular en nuestro municipio para clamar por la paz, exigir el fin de los conflictos armados y denunciar los genocidios y las crisis internacionales.'],
                ['title' => 'Espacios de debate y conferencias:', 'description' => 'Organizamos mesas asociativas comarcales, ponencias informativas y charlas abiertas al público para analizar las misiones de derechos humanos internacionales y buscar soluciones conjuntas.'],
            ],
        ],
        [
            'number' => '06',
            'title' => 'Dinamización Cultural y Concienciación Social',
            'intro' => 'Utilizamos la cultura y el encuentro comunitario como un puente derribar prejuicios, sembrar empatía y fomentar la convivencia.',
            'placeholder' => 'culture',
            'placeholder_label' => 'Fotografía pendiente: actividad cultural o evento solidario de convivencia.',
            'placeholder_caption' => 'Actividad cultural o evento solidario',
            'activities' => [
                ['title' => 'Cultura social y comunitaria:', 'description' => 'Impulsamos y damos soporte técnico a representaciones teatrales, proyecciones de documentales y cinefórum protagonizados o enfocados en las vivencias de personas migrantes y refugiadas.'],
                ['title' => 'Eventos solidarios de convivencia:', 'description' => 'Organizamos jornadas gastronómicas, mercadillos solidarios, eventos de ocio y ferias asociativas para recaudar fondos, visibilizar causas globales y estrechar los lazos vecinales en Caudete.'],
            ],
        ],
    ];
@endphp

@section('content')
    @if ($isCaudeteAboutPage)
        <article class="about-page" aria-labelledby="about-title">
            <section class="about-hero" aria-labelledby="about-title">
                <div class="about-hero-copy">
                    <p class="eyebrow"><span></span> Caudete Se Mueve</p>
                    <h1 id="about-title">{{ $page->title }}</h1>

                    <p class="about-intro">Caudete Se Mueve somos un colectivo vecinal y una organización sin ánimo de lucro nacida de la inquietud y la solidaridad de los ciudadanos de Caudete (Albacete). Creemos firmemente que, ante las crisis humanitarias, la injusticia social y la vulnerabilidad, la ciudadanía no puede quedarse de brazos cruzados:</p>

                    <p class="about-manifesto">debemos <span>movernos.</span></p>

                    <p class="about-continuation">Trabajamos de forma independiente, asamblearia y transparente, tejiendo redes de apoyo mutuo que conectan la solidaridad de nuestro pueblo con realidades complejas, tanto a la vuelta de la esquina como más allá de nuestras fronteras.</p>
                </div>

                @if ($image = $site->asset('quienes-somos/caudete-grupo-voluntarios.webp'))
                    <figure class="about-hero-photo">
                        <img src="{{ $image }}" alt="Voluntarias y voluntarios de Caudete Se Mueve colaboran en la entrega de ayuda humanitaria" fetchpriority="high">
                    </figure>
                @endif
            </section>

            <section class="about-principles" aria-label="Nuestra misión y nuestra visión">
                <div class="about-principles-inner">
                    <div class="about-principles-grid">
                        <section class="about-principle about-principle-mission" aria-labelledby="about-mission-title">
                            <div class="about-card-heading">
                                <span class="about-principle-icon" aria-hidden="true">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M20.8 8.6c0 5.1-8.8 10.2-8.8 10.2S3.2 13.7 3.2 8.6A4.5 4.5 0 0 1 12 6.4a4.5 4.5 0 0 1 8.8 2.2Z" />
                                        <path d="M7.3 12h2.2l1.2-2.1 2.2 4 1.2-1.9h2.5" />
                                    </svg>
                                </span>
                                <h2 id="about-mission-title">Nuestra misión</h2>
                            </div>
                            <p>Transformar la indignación en acción y la empatía en ayuda material y transformadora. Nos organizamos para dar respuestas rápidas y eficaces a emergencias humanitarias, garantizar la dignidad de las personas en situaciones de vulnerabilidad y concienciar a la sociedad civil para construir un mundo más justo y en paz.</p>
                        </section>

                        <section class="about-principle about-principle-vision" aria-labelledby="about-vision-title">
                            <div class="about-card-heading">
                                <span class="about-principle-icon" aria-hidden="true">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M2.5 12s3.4-6 9.5-6 9.5 6 9.5 6-3.4 6-9.5 6-9.5-6-9.5-6Z" />
                                        <circle cx="12" cy="12" r="2.5" />
                                    </svg>
                                </span>
                                <h2 id="about-vision-title">Nuestra visión</h2>
                            </div>
                            <p>Queremos ser un motor de cambio e inclusión en la comarca, consolidando un modelo de voluntariado activo, sostenible y en red. Aspiramos a una sociedad global donde los Derechos Humanos se defiendan activamente en la calle y las fronteras dejen de ser un obstáculo para la dignidad humana.</p>
                        </section>
                    </div>
                </div>
            </section>

            <section class="about-values" aria-labelledby="about-values-title">
                <div class="about-values-inner">
                    <header class="about-values-heading">
                        <h2 id="about-values-title">Nuestros valores</h2>
                        <p>Nuestra forma de actuar se basa en principios que guían nuestro compromiso con las personas, la solidaridad y la transformación social.</p>
                    </header>

                    <ul class="about-values-grid">
                        <li class="about-principle about-value-card">
                            <div class="about-card-heading">
                            <span class="about-principle-icon" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M20.8 8.6c0 5.1-8.8 10.2-8.8 10.2S3.2 13.7 3.2 8.6A4.5 4.5 0 0 1 12 6.4a4.5 4.5 0 0 1 8.8 2.2Z" />
                                    <path d="M12 8.8v5.4m-2.7-2.7h5.4" />
                                </svg>
                            </span>
                                <h3>Defensa Activa de los Derechos Humanos</h3>
                            </div>
                            <p>Los Derechos Humanos no se debaten, se defienden. Ponemos nuestro esfuerzo, nuestra voz y nuestros recursos al servicio de la dignidad de todas las personas, sin importar su origen, estatus o procedencia, entendiendo que los derechos universales deben ser garantizados en cualquier rincón del mundo.</p>
                        </li>

                        <li class="about-principle about-value-card">
                            <div class="about-card-heading">
                                <span class="about-principle-icon" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="m3 10 13-5v14L3 14v-4Z" />
                                    <path d="M16 9c2 .6 3 1.6 3 3s-1 2.4-3 3m-9-.8 1.4 5.3h3L10 16" />
                                </svg>
                            </span>
                                <h3>Denuncia de las Vulneraciones</h3>
                            </div>
                            <p>No somos cómplices del silencio. Sumamos a la acción humanitaria la labor irrenunciable de denunciar públicamente las injusticias, los genocidios, el racismo, la violencia de género y las violaciones de derechos que sufren los colectivos vulnerables y las personas en tránsito en las fronteras y rutas migratorias.</p>
                        </li>

                        <li class="about-principle about-value-card">
                            <div class="about-card-heading">
                                <span class="about-principle-icon" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M12 3v18m-7-14h14M5 7l-3 6h6L5 7Zm14 0-3 6h6l-3-6ZM8 21h8" />
                                    <path d="M2 13a3 3 0 0 0 6 0m8 0a3 3 0 0 0 6 0" />
                                </svg>
                            </span>
                                <h3>Compromiso con la Justicia Social</h3>
                            </div>
                            <p>Trabajamos por un modelo social justo, equitativo e integrador. Entendemos la solidaridad no como un acto de caridad vertical, sino como una herramienta horizontal de transformación que busca erradicar las desigualdades estructurales, tanto a nivel local en nuestro municipio como en el ámbito internacional.</p>
                        </li>

                        <li class="about-principle about-value-card">
                            <div class="about-card-heading">
                                <span class="about-principle-icon" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M12 20s-7.5-4.2-7.5-10A4.4 4.4 0 0 1 12 7.2 4.4 4.4 0 0 1 19.5 10c0 5.8-7.5 10-7.5 10Z" />
                                    <path d="M3 17h5l2-2 2 2h5l3-2" />
                                </svg>
                            </span>
                                <h3>Acción Directa y Solidaridad</h3>
                            </div>
                            <p>No somos un simple escaparate de buenas intenciones. Recogemos, clasificamos, reparamos, cargamos contenedores y transportamos la ayuda humanitaria allí donde se necesita, optimizando cada recurso al máximo.</p>
                        </li>

                        <li class="about-principle about-value-card">
                            <div class="about-card-heading">
                                <span class="about-principle-icon" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="5" r="2.5" />
                                    <circle cx="5" cy="18" r="2.5" />
                                    <circle cx="19" cy="18" r="2.5" />
                                    <path d="m10.8 7.2-4.5 8.6m6.9-8.6 4.5 8.6M7.5 18h9" />
                                </svg>
                            </span>
                                <h3>Trabajo en Red y Alianzas</h3>
                            </div>
                            <p>Creemos firmemente en la cooperación. Coordinamos esfuerzos de forma diaria con el tejido asociativo de nuestro municipio, plataformas de acción social de la provincia y redes de apoyo internacional, sumando de forma abierta con cualquier entidad para multiplicar el impacto social.</p>
                        </li>

                        <li class="about-principle about-value-card">
                            <div class="about-card-heading">
                                <span class="about-principle-icon" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M7 7H3l4-4m-4 4 4 4M3 7h7a4 4 0 0 1 4 4" />
                                    <path d="M17 17h4l-4 4m4-4-4-4m4 4h-7a4 4 0 0 1-4-4" />
                                    <path d="m9 16 3-8 3 8m-5-3h4" />
                                </svg>
                            </span>
                                <h3>Economía Circular y Sostenibilidad</h3>
                            </div>
                            <p>Proyectos como nuestro taller solidario de bicicletas demuestran que la reutilización y el reciclaje material pueden transformarse de manera directa en autonomía, transporte digno e inserción social.</p>
                        </li>

                        <li class="about-principle about-value-card">
                            <div class="about-card-heading">
                                <span class="about-principle-icon" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="m2 9 10-5 10 5-10 5L2 9Z" />
                                    <path d="M6 11v5c3.6 2.5 8.4 2.5 12 0v-5m4-2v6" />
                                </svg>
                            </span>
                                <h3>Educación en Valores</h3>
                            </div>
                            <p>Creemos que para cambiar el futuro hay que remover conciencias en el presente. Por ello, entramos en las escuelas, institutos y centros juveniles para educar a las nuevas generaciones en la empatía, el voluntariado y la cultura de la paz.</p>
                        </li>
                    </ul>
                </div>
            </section>
        </article>
    @elseif ($isCaudeteWorkPage)
        <article class="work-page" aria-labelledby="work-title">
            <section class="work-hero" aria-labelledby="work-title">
                <div class="work-hero-copy">
                    <p class="eyebrow work-eyebrow"><span></span> Solidaridad en acción</p>
                    <h1 id="work-title">Qué hacemos</h1>
                    <p class="work-intro">En Caudete Se Mueve canalizamos la energía solidaria de la ciudadanía a través de diversas áreas de intervención. Creemos que la transformación social requiere tanto de la acción material inmediata como de la denuncia pública y la educación a largo plazo. Nuestras principales líneas de trabajo son:</p>
                </div>

                @if ($image = $site->asset('que-hacemos/caudete-recogida-estufas-dana.webp'))
                    <figure class="work-hero-media">
                        <img src="{{ $image }}" alt="Voluntarias de Caudete Se Mueve entregan una estufa donada durante una recogida solidaria" fetchpriority="high">
                        <figcaption>La solidaridad de Caudete se convierte en ayuda concreta.</figcaption>
                    </figure>
                @endif
            </section>

            <div class="work-areas" aria-label="Áreas de actuación">
                @foreach ($workAreas as $area)
                    <section class="work-area{{ $loop->even ? ' work-area--reverse' : '' }}" aria-labelledby="work-area-{{ $area['number'] }}-title">
                        <figure class="work-media">
                            @if ($image = $site->asset($area['image'] ?? null))
                                <img src="{{ $image }}" alt="{{ $area['alt'] }}" loading="lazy">
                                <figcaption>{{ $area['caption'] }}</figcaption>
                            @else
                                <div class="work-placeholder work-placeholder--{{ $area['placeholder'] }}" role="img" aria-label="{{ $area['placeholder_label'] }}">
                                    <span class="work-placeholder-number" aria-hidden="true">{{ $area['number'] }}</span>
                                    <span class="work-placeholder-icon" aria-hidden="true">
                                        @switch($area['placeholder'])
                                            @case('bicycle')
                                                <svg viewBox="0 0 64 64" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><circle cx="15" cy="44" r="10"/><circle cx="49" cy="44" r="10"/><path d="m15 44 12-20 10 20H15l8-13h17l9 13M24 24h8m9 0h5"/></svg>
                                                @break
                                            @case('education')
                                                <svg viewBox="0 0 64 64" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="m6 24 26-13 26 13-26 13L6 24Z"/><path d="M17 30v15c9 7 23 7 32 0V30M58 24v17"/><path d="M58 41c-4 3-4 7 0 10 4-3 4-7 0-10Z"/></svg>
                                                @break
                                            @case('peace')
                                                <svg viewBox="0 0 64 64" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M11 32c8-2 13-9 17-19 4 6 9 9 16 9 6 0 10-3 13-7-1 11-7 18-18 20l13 11c-8 1-15-2-21-8-6 5-13 7-21 5l10-8-9-3Z"/><path d="m27 20 7 10m-9 3 13-7"/></svg>
                                                @break
                                            @default
                                                <svg viewBox="0 0 64 64" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M8 18h19v28H8zM37 18h19v28H37z"/><path d="M13 24h9m-9 6h9m-9 6h9m20-12h9m-9 6h9m-9 6h9M4 52h56"/><circle cx="17.5" cy="49" r="4"/><circle cx="46.5" cy="49" r="4"/></svg>
                                        @endswitch
                                    </span>
                                    <span class="work-placeholder-label">Fotografía pendiente</span>
                                    <span class="work-placeholder-caption">{{ $area['placeholder_caption'] }}</span>
                                </div>
                            @endif
                        </figure>

                        <div class="work-area-copy">
                            <p class="work-area-number"><span>{{ $area['number'] }}</span> <span aria-hidden="true">/ 06</span></p>
                            <h2 id="work-area-{{ $area['number'] }}-title">{{ $area['title'] }}</h2>
                            <p class="work-area-intro">{{ $area['intro'] }}</p>
                            <div class="work-activities">
                                @foreach ($area['activities'] as $activity)
                                    <div class="work-activity">
                                        <h3>{{ $activity['title'] }}</h3>
                                        <p>{{ $activity['description'] }}</p>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </section>
                @endforeach
            </div>

            <section class="work-invitation" aria-labelledby="work-invitation-title">
                <span class="work-invitation-mark" aria-hidden="true">+</span>
                <div class="work-invitation-copy">
                    <p class="work-invitation-eyebrow">Participa</p>
                    <h2 id="work-invitation-title">La solidaridad se construye entre todos.</h2>
                </div>
                <div class="work-invitation-action">
                    <p>Consulta cómo colaborar con Caudete Se Mueve a través de su página de donaciones.</p>
                    <a class="work-invitation-link" href="{{ route('pages.show', ['page' => 'donaciones']) }}">Ir a Donaciones <span aria-hidden="true">→</span></a>
                </div>
            </section>
        </article>
    @elseif ($isCaudeteDonationsPage)
        <article class="donations-page" aria-labelledby="donations-title">
            <section class="donations-hero" aria-labelledby="donations-title">
                <div class="donations-hero-copy">
                    <p class="eyebrow donations-eyebrow">Colabora con nosotros</p>
                    <h1 id="donations-title">Cómo puedes ayudar</h1>
                    <p class="donations-lead">Únete / Cómo colaborar</p>
                    <p>En <strong>Desde Caudete Se Mueve</strong> no existen las estructuras rígidas: cualquier persona, con el tiempo o los recursos de los que disponga, puede ser un motor de cambio. Tu implicación es lo que hace posible que la solidaridad de nuestro pueblo llegue a cualquier parte del mundo.</p>
                    <p>Descubre las diferentes formas en las que puedes arrimar el hombro:</p>
                </div>
                <div class="donations-hero-mark">
                    <h2>Hay muchas formas de colaborar</h2>
                    <div class="donations-options-grid">
                        <div class="donations-option">
                            <span class="donations-option-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M12 21s-7-4.4-7-10a4 4 0 0 1 7-2.6A4 4 0 0 1 19 11c0 5.6-7 10-7 10Z"/><path d="M8 12h2l1-2 2 4 1-2h2"/></svg></span>
                            <div><h3><a href="#donations-voluntariado">Voluntariado</a></h3><p>Dedica tu tiempo y experiencia</p></div>
                        </div>
                        <div class="donations-option">
                            <span class="donations-option-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M3 7 12 3l9 4-9 4-9-4Z"/><path d="M3 7v10l9 4 9-4V7M12 11v10"/><path d="m7.5 5 9 4"/></svg></span>
                            <div><h3><a href="#donations-materiales">Donaciones</a></h3><p>Aporta materiales que puedan ayudar</p></div>
                        </div>
                        <div class="donations-option">
                            <span class="donations-option-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M4 11V6l13-3v18L4 17v-6Z"/><path d="M4 11h13M8 18l1.5 4h3L11 17"/><path d="M20 8c1.2 1 1.2 5 0 6"/></svg></span>
                            <div><h3><a href="#donations-movilizaciones">Movilizaciones</a></h3><p>Haz visible tu compromiso</p></div>
                        </div>
                        <div class="donations-option">
                            <span class="donations-option-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="M3 10h18M7 15h3"/></svg></span>
                            <div><h3><a href="#donations-apoyo-economico">Apoyo económico</a></h3><p>Contribuye a mantener los proyectos</p></div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="donations-ways" aria-label="Formas de colaborar">
                <section class="donations-way" id="donations-voluntariado" aria-labelledby="donation-volunteer-title">
                    <div class="donations-way-heading">
                        <span class="donations-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M12 21s-7-4.4-7-10a4 4 0 0 1 7-2.6A4 4 0 0 1 19 11c0 5.6-7 10-7 10Z"/><path d="M8 12h2l1-2 2 4 1-2h2"/></svg></span>
                        <div><p class="donations-number">01 · Participación</p><h2 id="donation-volunteer-title">Hazte Voluntario/a</h2><p class="donations-subtitle">Pon tus manos en acción</p></div>
                    </div>
                    <p>Si quieres comprometerte de forma activa, te necesitamos en el día a día de nuestro colectivo. El trabajo es variado y siempre se adapta a tus posibilidades:</p>
                    <ul class="donations-detail-list">
                        <li><strong>Logística y almacén:</strong> Ayúdanos en las jornadas de recepción de material, clasificación textil (triaje de ropa de abrigo) y el paletizado técnico para la carga de nuestros contenedores humanitarios.</li>
                        <li><strong>Taller mecánico solidario:</strong> Si tienes nociones de mecánica de bicicletas o simplemente quieres aprender, puedes colaborar en la puesta a punto y reparación de las bicicletas que entregamos a colectivos vulnerables.</li>
                        <li><strong>Eventos y sensibilización:</strong> Participa en la organización de nuestros mercadillos solidarios, eventos culturales, stands informativos o impartiendo charlas en los centros educativos.</li>
                    </ul>
                </section>

                <section class="donations-way" id="donations-materiales" aria-labelledby="donation-material-title">
                    <div class="donations-way-heading">
                        <span class="donations-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M3 7 12 3l9 4-9 4-9-4Z"/><path d="M3 7v10l9 4 9-4V7M12 11v10"/><path d="m7.5 5 9 4"/></svg></span>
                        <div><p class="donations-number">02 · Recursos</p><h2 id="donation-material-title">Donaciones Materiales</h2><p class="donations-subtitle">Lo que tú ya no usas, aquí transforma vidas</p></div>
                    </div>
                    <p>Periódicamente activamos campañas específicas según las necesidades más urgentes que nos trasladan desde los campos de refugio o las redes de distribución local. Puedes colaborar aportando:</p>
                    <ul class="donations-detail-list">
                        <li><strong>Bicicletas en desuso:</strong> No importa si necesitan una puesta a punto. Las recogemos, las reparamos en nuestro taller y las convertimos en herramientas de transporte digno y autonomía.</li>
                        <li><strong>Alimentos no perecederos:</strong> Participa aportando alimentos de primera necesidad en nuestras recogidas escolares, en los supermercados de Caudete o en los puntos de recogida locales autorizados.</li>
                        <li><strong>Ropa técnica y de abrigo:</strong> Durante los periodos de campaña, recogemos mantas, sacos de dormir, chaquetas de invierno y calzado resistente para las rutas migratorias europeas. <em>(Consulta en nuestras redes sociales o sección de noticias si hay una campaña textil activa en este momento).</em></li>
                    </ul>
                </section>

                <section class="donations-way" id="donations-movilizaciones" aria-labelledby="donation-mobilizations-title">
                    <div class="donations-way-heading">
                        <span class="donations-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M4 11V6l13-3v18L4 17v-6Z"/><path d="M4 11h13M8 18l1.5 4h3L11 17"/><path d="M20 8c1.2 1 1.2 5 0 6"/></svg></span>
                        <div><p class="donations-number">03 · Acción colectiva</p><h2 id="donation-mobilizations-title">Participa en las Movilizaciones</h2><p class="donations-subtitle">Pon tu voz</p></div>
                    </div>
                    <p>La defensa de la justicia social y los Derechos Humanos también se ejerce ocupando el espacio público de manera pacífica.</p>
                    <ul class="donations-detail-list">
                        <li><strong>Concentraciones mensuales:</strong> Súmate a nuestras movilizaciones ciudadanas bajo el lema "No a la Guerra". Tu presencia en la calle es vital para romper el silencio, denunciar las vulneraciones de derechos internacionales y visibilizar las crisis humanitarias.</li>
                    </ul>
                </section>

                <section class="donations-way donations-way--economic" id="donations-apoyo-economico" aria-labelledby="donation-economic-title">
                    <div class="donations-way-heading">
                        <span class="donations-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="M3 10h18M7 15h3"/></svg></span>
                        <div><p class="donations-number">04 · Apoyo a proyectos</p><h2 id="donation-economic-title">Colaboración Económica</h2><p class="donations-subtitle">Apoya nuestros proyectos</p></div>
                    </div>
                    <p>Somos un colectivo totalmente independiente . Cada euro recaudado se destina íntegramente a sufragar los costes logísticos de los envíos (tasas de contenedores marítimos, combustible de furgonetas de ayuda, herramientas para el taller , compra de alimentos)</p>
                    <ul class="donations-detail-list">
                        <li><strong>Eventos benéficos:</strong> Ven a disfrutar de nuestras jornadas de "Tapa Solidaria", eventos de ocio o compra en nuestros mercadillos del Día del Refugiado.</li>
                        <li><strong>Fila Cero / Donaciones:</strong> Puedes realizar una aportación económica puntual a través de nuestros canales oficiales en las campañas de emergencia y también a través de nuestra cuenta en CaixaBank</li>
                    </ul>
                    <div class="donations-bank" aria-label="Datos bancarios">
                        <p class="donations-bank-label">Cuenta de CaixaBank</p>
                        <p><strong>Titular</strong>, Plataforma Ciudadana Caudete se Mueve.</p>
                        <p class="donations-iban-label">IBAN</p>
                        <p class="donations-iban">ES90 2100 7984 8301 0017 9937</p>
                    </div>
                </section>
            </section>

            <section class="donations-contact" aria-labelledby="donations-contact-title">
                <div class="donations-contact-copy">
                    <p class="donations-number">Estamos aquí para ayudarte</p>
                    <h2 id="donations-contact-title">¿Quieres dar el paso? ¡Contáctanos!</h2>
                    <p>Si tienes dudas, quieres traer una bicicleta o quieres sumarte a la próxima jornada de voluntariado en el almacén, no dudes en escribirnos:</p>
                </div>
                <div class="donations-contact-details">
                    <p><span class="donations-contact-label">Punto de encuentro/Almacén</span><span>Isaac Peral, 10</span></p>
                    <p><span class="donations-contact-label">Correo electrónico</span><a href="mailto:laemi_61@hotmail.com">laemi_61@hotmail.com</a></p>
                    <div class="donations-social"><span class="donations-contact-label">Redes Sociales</span><a href="https://www.facebook.com/caudetemueve" target="_blank" rel="noopener noreferrer">Facebook <span aria-hidden="true">↗</span></a><a href="https://www.instagram.com/caudetesemueve/" target="_blank" rel="noopener noreferrer">Instagram <span aria-hidden="true">↗</span></a></div>
                </div>
            </section>

            <section class="donations-guide" aria-labelledby="donations-guide-title">
                <header class="donations-guide-heading">
                    <p class="eyebrow donations-eyebrow">Antes de donar</p>
                    <h2 id="donations-guide-title">Guía práctica para donaciones responsables</h2>
                    <p class="donations-guide-quote">"Donar es un acto de justicia y dignidad, no de caridad"</p>
                    <p>Cuando preparamos un envío humanitario o un reparto local, nuestro objetivo número uno es <strong>garantizar la dignidad de las personas que lo van a recibir</strong>. El material que enviamos a campos de refugiados, rutas migratorias o familias vulnerables debe ser una herramienta útil y de calidad, capaz de resistir condiciones extremas.</p>
                    <p>Para ayudarnos a optimizar el trabajo de nuestro equipo de voluntarios en el almacén, te pedimos que sigas esta sencilla guía antes de traernos tus donaciones:</p>
                </header>

                <div class="donations-guide-grid">
                    <section class="donations-guide-card" aria-labelledby="guide-bikes-title">
                        <p class="donations-number">01 · Movilidad</p><h3 id="guide-bikes-title">Donación de Bicicletas <span>(Proyecto de Movilidad)</span></h3>
                        <div class="donations-accept"><h4><span aria-hidden="true">✓</span> Lo que SÍ necesitamos</h4><p>Bicicletas de cualquier tamaño (especialmente de adulto), aunque tengan piezas desgastadas, óxido superficial o necesiten ajustes. Nuestro taller solidario las pondrá a punto mecánicamente.</p></div>
                        <div class="donations-reject"><h4><span aria-hidden="true">×</span> Lo que NO podemos aceptar</h4><p>Bicicletas con el cuadro partido, fisurado o gravemente deformado cuya reparación comprometa la seguridad de la persona que la va a utilizar.</p></div>
                    </section>
                    <section class="donations-guide-card" aria-labelledby="guide-clothes-title">
                        <p class="donations-number">02 · Campañas textiles</p><h3 id="guide-clothes-title">Donación de Ropa y Calzado <span>(Campañas Textiles)</span></h3>
                        <p class="donations-campaign-note"><em>Recuerda traer material únicamente cuando haya una campaña textil activa en la web o redes sociales.</em></p>
                        <div class="donations-accept"><h4><span aria-hidden="true">✓</span> Lo que SÍ necesitamos</h4><p>Ropa técnica de abrigo (anoraks, chaquetones impermeables), mantas, sacos de dormir en buen estado, calzado resistente de montaña o deportivo, y ropa cómoda/deportiva. Las prendas deben estar limpias y con las cremalleras operativas.</p></div>
                        <div class="donations-reject"><h4><span aria-hidden="true">×</span> Lo que NO podemos aceptar</h4><p>Ropa rota, sucia, desgastada o con manchas imposibles de quitar. Tampoco aceptamos ropa de fiesta, trajes formales, tacones o prendas de vestir que no respondan a una necesidad real de abrigo o comodidad en situaciones de vulnerabilidad.</p></div>
                        <blockquote class="donations-golden-rule"><strong>La regla de oro:</strong> <em>Si no se lo pondrías a un ser querido tuyo, no lo dones para el contenedor.</em></blockquote>
                    </section>
                    <section class="donations-guide-card" aria-labelledby="guide-food-title">
                        <p class="donations-number">03 · Red local y emergencias</p><h3 id="guide-food-title">Donación de Alimentos</h3>
                        <div class="donations-accept"><h4><span aria-hidden="true">✓</span> Lo que SÍ necesitamos</h4><p>Alimentos no perecederos envasados de forma segura (legumbres, arroz, pasta, aceite, conservas en lata, leche en polvo, etc.). Si tienes un excedente agrícola o comercial con fecha de caducidad corta, avísanos con urgencia para activar nuestra red de distribución rápida local.</p></div>
                        <div class="donations-reject"><h4><span aria-hidden="true">×</span> Lo que NO podemos aceptar</h4><p>Alimentos cuya fecha de consumo preferente o caducidad ya haya pasado, envases de vidrio o cartón que estén abiertos, abollados, dañados o que presenten pérdidas de contenido.</p></div>
                    </section>
                    <section class="donations-guide-card" aria-labelledby="guide-hygiene-title">
                        <p class="donations-number">04 · Primera necesidad</p><h3 id="guide-hygiene-title">Productos de Higiene y Primera Necesidad</h3>
                        <div class="donations-accept"><h4><span aria-hidden="true">✓</span> Lo que SÍ necesitamos</h4><p>Productos de higiene personal nuevos y precintados (gel, champú, dentífrico, compresas, pañales) y material de primeros auxilios.</p></div>
                        <div class="donations-reject"><h4><span aria-hidden="true">×</span> Lo que NO podemos aceptar</h4><p>Botes de higiene ya empezados o abiertos, medicamentos caducados o que requieran receta e instrucciones médicas específicas.</p></div>
                    </section>
                </div>

                <section class="donations-why" aria-labelledby="donations-why-title">
                    <header><p class="eyebrow donations-eyebrow">Solidaridad responsable</p><h3 id="donations-why-title">¿Por qué es tan importante seguir esta guía?</h3></header>
                    <div class="donations-why-grid">
                        <article><span class="donations-why-number">01</span><h4>Por Respeto y Dignidad</h4><p>Las personas en situación de vulnerabilidad merecen recibir artículos útiles y en perfecto estado de uso. La solidaridad horizontal se basa en el respeto mutuo.</p></article>
                        <article><span class="donations-why-number">02</span><h4>Por Eficiencia Logística</h4><p>Cada minuto que nuestros voluntarios pasan seleccionando o descartando ropa inservible en el almacén es un minuto menos que dedicamos a estibar contenedores o coordinar repartos de emergencia.</p></article>
                        <article><span class="donations-why-number">03</span><h4>Por Sostenibilidad Económica</h4><p>Transportar material que no sirve consume espacio físico en los palets y genera costes de transporte marítimo muy elevados que pagamos entre todos. Optimizar la carga es clave para que los contenedores lleguen a su destino.</p></article>
                    </div>
                </section>
            </section>
        </article>
    @else
    <article class="editorial-page">
        <header class="page-heading">
        <p class="eyebrow">Caudete Se Mueve</p><h1>
            {{ $page->title }}
        </h1>
        </header>

        <div class="prose-body mt-8 text-stone-700">
            @include('public.partials.rich-content', ['content' => $page->body])
        </div>
    </article>
    @endif
@endsection
