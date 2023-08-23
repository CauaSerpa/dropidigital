<div class="page__header center">
    <div class="header__title">
        <div class="page__header center">
            <div class="header__title">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb align-items-center mb-3">
                        <li class="breadcrumb-item"><a href="<?php echo INCLUDE_PATH_DASHBOARD ?>produtos" class="fs-5 text-decoration-none text-reset">Produtos</a></li>
                        <li class="breadcrumb-item fs-4 fw-semibold active" aria-current="page">Categorias</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    <div class="header__actions">
        <a href="<?php echo INCLUDE_PATH_DASHBOARD; ?>" class="export text-black text-decoration-none">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" style="fill: rgba(0, 0, 0, 1);transform: ;msFilter:;"><path d="m12 18 4-5h-3V2h-2v11H8z"></path><path d="M19 9h-4v2h4v9H5v-9h4V9H5c-1.103 0-2 .897-2 2v9c0 1.103.897 2 2 2h14c1.103 0 2-.897 2-2v-9c0-1.103-.897-2-2-2z"></path></svg>
            Importar
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" style="fill: rgba(0, 0, 0, 1);transform: ;msFilter:;"><path d="M16.293 9.293 12 13.586 7.707 9.293l-1.414 1.414L12 16.414l5.707-5.707z"></path></svg>
        </a>
        <div class="container__button">
            <a href="<?php echo INCLUDE_PATH_DASHBOARD; ?>criar-produto" class="button button--flex new text-decoration-none">+ Criar Produto</a>
        </div>
    </div>
</div>

<div class="card__container grid one tabPanel" style="display: grid;">
    <div class="card__box grid">
        <div class="card table">
            <div class="card__title">
                <div class="title__content grid">
                    <form action="" class="table__actions">
                        <div class="search__container">
                            <input type="text" name="searchUsers" id="searchUsers" class="search" placeholder="Pesquisar" title="Pesquisar">
                        </div>
                        <button type="button" class="filter"data-bs-toggle="offcanvas" data-bs-target="#offcanvas" aria-controls="offcanvasExample">
                            Filtrar
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" style="fill: rgba(0, 0, 0, 1);transform: ;msFilter:;"><path d="M21 3H5a1 1 0 0 0-1 1v2.59c0 .523.213 1.037.583 1.407L10 13.414V21a1.001 1.001 0 0 0 1.447.895l4-2c.339-.17.553-.516.553-.895v-5.586l5.417-5.417c.37-.37.583-.884.583-1.407V4a1 1 0 0 0-1-1zm-6.707 9.293A.996.996 0 0 0 14 13v5.382l-2 1V13a.996.996 0 0 0-.293-.707L6 6.59V5h14.001l.002 1.583-5.71 5.71z"></path></svg>
                        </button>
                    </form>
                </div>
            </div>
            <table>
                <thead>
                    <tr>
                        <th class="checkbox"><input type="checkbox" name="checkbox" id="checkbox" class="checkbox"></th>
                        <th>Nome</th>
                        <th>E-mail Público</th>
                        <th>Link</th>
                        <th>Descrição</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    
                </tbody>
            </table>
        </div>
        <div class="center">
            <div class="left">
                <div class="container__button">
                    <div class="limitPageDropdown dropdown button button--flex select">
                        <input type="text" class="text02" placeholder="10" readonly="">
                        <div class="option">
                            <div onclick="show('10')">10</div>
                            <div onclick="show('20')">20</div>
                            <div onclick="show('30')">30</div>
                            <div onclick="show('40')">40</div>
                            <div onclick="show('50')">50</div>
                        </div>
                    </div>
                    <label>Produtos por página</label>
                </div>
            </div>
            <div class="right grid">
                <div class="controller"><span class="analog pag-link active pag-link">1</span></div>            </div>
        </div>
    </div>
</div>