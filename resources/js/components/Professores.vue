<template>
  <!-- visualizar professor -->
  <v-dialog v-model="mostrarPopup">
    <v-card class="professores">
      <div class="botaoCancelar" icon @click="fecharPopup">
        <button color="#FFFFFF" text @click="close">
          <img src=".\img\X.png">
        </button>
      </div>
      <v-card-title>
        <v-spacer></v-spacer>
        <div class="logoForm">
          <img src=".\img\logoPortal.png" alt="Logo UniFil">
        </div>
        <div class="alignTitleForm">
          Professores
        </div>
        <div class="descricao">
          <p>Professores cadastrados no sistema.</p>
        </div>
      </v-card-title>

      <!-- Barra de pesquisa -->
      <v-card-title>
        <v-text-field class="inputForms" v-model="search" append-icon="mdi-magnify" label="Pesquisar" single-line
          hide-details></v-text-field>
      </v-card-title>

      <!-- Tabela de professores -->
      <div class="conteudo">
        <div class="scroll-area">
          <v-data-table :headers="headers" :items="professores" v-model:page="paginaAtual"
            v-model:items-per-page="itemsPerPage" :items-per-page-options="[5, 10, 15, 20, -1]" :search="search"
            :sort-by="sortBy" :sort-desc="sortDesc" density="compact">
            <!-- Cabeçalhos personalizados -->
            <template v-slot:headers="{ columns }">
              <tr>
                <th style="width: 113px; height: 20px;">NOME PROFESSOR</th>
                <th style="width: 113px; height: 20px;">CPF</th>
                <th style="width: 113px; height: 20px;">MATRÍCULA</th>
                <th style="width: 57px; height: 20px;"></th>
              </tr>
            </template>

            <!-- Células com largura personalizada -->
            <template v-slot:item.nome="{ item }">
              <td style="width: 113px;">{{ item.nome }}</td>
            </template>

            <template v-slot:item.cpf="{ item }">
              <td style="width: 113px;">{{ item.cpf }}</td>
            </template>

            <template v-slot:item.matricula="{ item }">
              <td style="width: 113px;">{{ item.matricula }}</td>
            </template>

            <template v-slot:top>
              <v-dialog v-model="dialog" max-width="456px">

                <!-- inserir/ editar professor -->
                <v-card class="professores">
                  <div class="botaoCancelar">
                    <button color="#FFFFFF" text @click="close">
                      <img src=".\img\X.png">
                    </button>
                  </div>
                  <v-card-title>
                    <div class="logoForm">
                      <img src=".\img\logoPortal.png">
                    </div>
                    <span class="alignTitleForm">
                      {{ formTitle }}
                    </span>
                    <div class="descricao">
                      {{ formDescription }}
                    </div>
                  </v-card-title>
                  <v-card-text>
                    <v-container>
                      <v-col>
                        <v-row cols="1" md="4" lg="4" xl="4" xxl="4">
                          <v-text-field class="inputForms" v-model="editedItem.nome"
                            label="NOME PROFESSOR *"
                            :error-messages="mostrarErroNome ? 'Nome inválido' : ''"
                            @blur="mostrarErroNome = editedItem.nome !== '' && !validarNome(editedItem.nome)"></v-text-field>
                        </v-row>
                        <v-row cols="1" md="4" lg="4" xl="4" xxl="4">
                          <v-text-field class="inputForms" v-model="editedItem.cpf" label="CPF *"
                            :error-messages="mostrarErroCPF ? 'CPF inválido' : ''"
                            @blur="mostrarErroCPF = editedItem.cpf !== '' && !validarCPF(editedItem.cpf)"
                            @input="editedItem.cpf = formatarCPF(editedItem.cpf)" />
                        </v-row>
                        <v-row cols="1" md="4" lg="4" xl="4" xxl="4">
                          <v-text-field class="inputForms" v-model="editedItem.matricula"
                            label="MATRICULA"></v-text-field>
                        </v-row>
                      </v-col>
                    </v-container>
                  </v-card-text>
                  <v-card-actions>
                    <v-btn class="botaoCinza" color="#FFFFFF" @click="save">
                      {{ salvar }}
                    </v-btn>
                  </v-card-actions>
                </v-card>
              </v-dialog>

              <!-- popup certeza de exclusão -->
              <v-dialog v-model="dialogDelete" max-width="456px">
                <v-card>
                  <v-card-title class="alignTitleForm">
                    <p>Tem certeza que deseja excluir <br>esse professor?</p>
                  </v-card-title>
                  <v-card-actions>
                    <v-spacer></v-spacer>
                    <div class="botaoCerteza">
                      <v-btn text @click="closeDelete">Cancelar</v-btn>
                    </div>
                    <div class="botaoCerteza">
                      <v-btn text @click="deleteItemConfirm">Sim</v-btn>
                    </div>
                    <v-spacer></v-spacer>
                  </v-card-actions>
                </v-card>
              </v-dialog>
            </template>

            <!-- Icones de editar e excluir item -->
            <template v-slot:item.actions="{ item }">
              <div class="d-flex align-center" style="justify-content: center;">
                <button class="botaoIcone" @click="linkItem(item)"><img style="width: 13px;"
                    src="C:\laragon\www\sistema-horarios\resources\js\components\img\layout.png" alt="horario"></button>
                <button class="botaoIcone" @click="editItem(item)"><img style="width: 15px;"
                    src="C:\laragon\www\sistema-horarios\resources\js\components\img\editing.png" alt="editar"></button>
                <button class="botaoIcone" @click="deleteItem(item)"><img style="width: 15px;"
                    src="C:\laragon\www\sistema-horarios\resources\js\components\img\trash.png" alt="horario"></button>
              </div>
            </template>
          </v-data-table>
        </div>
      </div>
      <v-btn class="botaoCinza" color="#616161" v-bind="attrs" @click="newItem">
        Adicionar Novo
      </v-btn>
    </v-card>
  </v-dialog>
</template>

<script>
export default {
  data() {
    return {
      mostrarPopup: false,
      search: '',
      sortBy: [{ key: 'nome', order: 'asc' }],
      sortDesc: false,
      dialog: false,
      dialogDelete: false,
      paginaAtual: 1,
      itemsPerPage: 5,
      mostrarErroCPF: false,
      mostrarErroNome: false,
      headers: [
        {
          title: 'NOME PROFESSOR', align: 'start',
          sortable: false, key: 'nome'
        },
        { title: 'CPF', key: 'cpf' },
        { title: 'MATRICULA', key: 'matricula' },
        { text: 'Actions', key: 'actions', sortable: false },
      ],
      professores: [],
      editedIndex: -1,
      editedItem: {
        nome: '',
        cpf: '',
        matricula: '',
      },
      defaultItem: {
        nome: '',
        cpf: '',
        matricula: '',
      },
    }
  }
  ,
  mounted() {
    this.carregarProfessores();
  },
  computed: {
    formTitle() {
      return this.editedIndex === -1 ? 'Adicionar Professor' : 'Editar Professor'
    },
    formDescription() {
      return this.editedIndex === -1 ? 'Insira as informações necessárias para o cadastro do professor.' : 'Insira as informações necessárias para a edição do professor.'
    },
    salvar() {
      return this.editedIndex === -1 ? 'Inserir' : 'Editar'
    },
  },

  watch: {
    dialog(val) {
      val || this.close()
    },
    dialogDelete(val) {
      val || this.closeDelete()
    },
  },

  created() {
    this.initialize()
  }
  ,
  methods: {
    abrirPopup() {
      this.mostrarPopup = true;
    },
    fecharPopup() {
      this.mostrarPopup = false;
    },
    carregarProfessores() {
      fetch('/api/professores')
        .then(res => res.json())
        .then(data => this.professores = data);
    },
    validarNome(nome) {
      if (nome.value === '') return false;
      return true;
    },
    validarCPF(cpf) {
      cpf = cpf.replace(/[^\d]+/g, '');
      if (cpf.length !== 11) return false;
      if (/^(\d)\1+$/.test(cpf)) return false;

      let soma = 0;
      let resto;

      for (let i = 1; i <= 9; i++) {
        soma += parseInt(cpf.substring(i - 1, i)) * (11 - i);
      }
      resto = (soma * 10) % 11;
      if (resto === 10 || resto === 11) resto = 0;
      if (resto !== parseInt(cpf.substring(9, 10))) return false;

      soma = 0;
      for (let i = 1; i <= 10; i++) {
        soma += parseInt(cpf.substring(i - 1, i)) * (12 - i);
      }
      resto = (soma * 10) % 11;
      if (resto === 10 || resto === 11) resto = 0;
      if (resto !== parseInt(cpf.substring(10, 11))) return false;

      return true;
    },
    CPFjaExiste(cpf) {
      cpf = cpf.replace(/[^\d]+/g, '');
      return this.professores.some(p =>
        p.cpf === cpf && p.id !== this.editedItem.id
      );
    },
    matriculaJaExiste(matricula) {
      return this.professores.some(p =>
        p.matricula === matricula && p.id !== this.editedItem.id
      );
    },
    newItem() {
      this.editedIndex = -1;
      this.editedItem = Object.assign({}, this.defaultItem);
      this.dialog = true;
    },
    editarProfessor(professor) {
      this.editando = true;
      this.mostrarForm = true;
      this.form = { ...professor };
    },
    initialize() {
      this.carregarProfessores()
    },
    carregarProfessores() {
      fetch('/api/professores')
        .then(res => res.json())
        .then(data => this.professores = data)
    },
    linkItem(item) {
      this.editedIndex = this.professores.indexOf(item)
      this.editedItem = Object.assign({}, item)
      /* colocar o link para a pagina do horário do respectivo professor */
    },
    editItem(item) {
      this.editedIndex = this.professores.indexOf(item)
      this.editedItem = Object.assign({}, item)
      this.dialog = true
    },

    deleteItem(item) {
      this.editedIndex = this.professores.indexOf(item)
      this.editedItem = Object.assign({}, item)
      this.dialogDelete = true
    },

    deleteItemConfirm() {
      fetch(`/api/professores/${this.editedItem.id}`, {
        method: 'DELETE',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
        }
      })
        .then(() => {
          this.carregarProfessores();
          this.closeDelete();
        })
        .catch(err => {
          console.error(err);
          alert('Erro ao deletar professor');
        });
    },

    close() {
      this.dialog = false
      this.$nextTick(() => {
        this.editedItem = Object.assign({}, this.defaultItem)
        this.editedIndex = -1
      })
    },

    closeDelete() {
      this.dialogDelete = false
      this.$nextTick(() => {
        this.editedItem = Object.assign({}, this.defaultItem)
        this.editedIndex = -1
      })
    },

    save() {
      if (!this.validarNome(this.editedItem.nome)) {
        alert('O campo nome não pode ficar vazio! Por favor, insira um nome válido.');
        return;
      }

      if (!this.validarCPF(this.editedItem.cpf)) {
        alert('CPF inválido! Por favor, insira um CPF válido.');
        return;
      }

      if (this.CPFjaExiste(this.editedItem.cpf)) {
        alert('CPF já cadastrado!');
        return;
      }

      if (this.matriculaJaExiste(this.editedItem.matricula)) {
        alert('Matrícula já cadastrada!');
        return;
      }

      this.editedItem.cpf = this.editedItem.cpf.replace(/[^\d]+/g, '');

      const url = this.editedIndex > -1
        ? `/api/professores/${this.editedItem.id}`
        : '/api/professores';
      const method = this.editedIndex > -1 ? 'PUT' : 'POST';

      fetch(url, {
        method,
        headers: {
          'Content-Type': 'application/json',
          Accept: 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
        },
        body: JSON.stringify(this.editedItem)
      })
        .then(res => res.json())
        .then(() => {
          this.carregarProfessores();
          this.close();
        })
        .catch(err => {
          console.error(err);
          alert('Erro ao salvar professor');
        });
    },
    formatarCPF(cpf) {
      cpf = cpf.replace(/\D/g, ''); // Remove tudo que não é dígito
      cpf = cpf.slice(0, 11); // Limita a 11 dígitos
      cpf = cpf.replace(/(\d{3})(\d)/, '$1.$2');
      cpf = cpf.replace(/(\d{3})(\d)/, '$1.$2');
      cpf = cpf.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
      return cpf;
    }
  }
}
</script>

<style scoped>
.professores {
  width: 456px;
  height: 566px;
  position: fixed;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  display: flex;
  justify-content: center;
  align-items: center;
}

.conteudo {
  max-width: 357px;
}

:deep(.v-data-table) {
  font-size: 12px;
  font-family: 'Roboto', sans-serif;
  font-weight: 400;
  table-layout: auto !important;
}

:deep(.v-data-table thead th),
:deep(.v-data-table tbody td) {
  padding: 0 !important;
  white-space: nowrap;
}

:deep(.v-data-table .v-data-table__td),
:deep(.v-data-table .v-data-table__th) {
  height: 20px !important;
  font-size: 12px;
}

:deep(.v-data-table .v-data-table__td),
:deep(.v-data-table .v-data-table__th) {
  border: 0.5px solid #d9d9d9;
}

:deep(.v-data-table th:nth-child(1)),
:deep(.v-data-table th:nth-child(2)),
:deep(.v-data-table th:nth-child(3)) {
  text-align: center;
  border: 0.5px solid #d9d9d9;
}

:deep(.v-data-table-footer) {
  padding: 0px 0px;
  min-height: 24px;
  font-size: 12px;
  display: flex;
  justify-content: space-between;
  text-align: left;
  height: 20px;
  align-items: center;           
  flex-wrap: nowrap;    
  gap: 4px; 
}

:deep(.v-data-table-footer__items-per-page) {
  padding: 0 4px 0 0;
  display: flex;
  align-items: center;
  white-space: nowrap;
  margin: 0;
}

:deep(.v-data-table-footer__info) {
  padding: 0 4px 0 0;
  display: flex;
  align-items: center;
  white-space: nowrap;
  margin: 0;
  min-width: 40px;
  justify-content: center;
}

:deep(.v-data-table-footer__pagination) {
  font-size: 12px;
  height: 20px;
  padding: 0 8px; 
  display: flex;
  align-items: center;
  white-space: nowrap;
  margin: 0;
  gap: 4px;
}

:deep(.v-pagination__list) {
  gap: 0px;
  display: flex;
}

:deep(.v-pagination__list li) {
  margin: 0 !important;
  padding: 0 !important;
}

:deep(.v-pagination__list .v-btn) {
  min-width: 28px; 
  height: 28px; 
  padding: 0 2px;
}

:deep(.v-data-table-footer .v-input) {
  width: 40px !important; 
  margin: 0 !important;
}

:deep(.v-data-table-footer .v-input__control) {
  display: flex;
  width: 40px !important;
  padding: 0 !important;
  margin: 0 !important;
}


:deep(.v-data-table-footer .v-field) {
  max-width: 40px;
  height: 20px;
  padding: 0 !important;
  margin: 0 !important;
  font-size: 12px;
  display: flex;
  align-items: center;
}

:deep(.v-data-table-footer .v-field__input) {
  height: 20px !important;
  padding: 0 4px !important;
  font-size: 12px !important;
  display: flex;
  align-items: center;
}

:deep(.v-select__menu-icon) {
  font-size: 16px;
}

:deep(.v-field__loader) {
  height: 0px !important;
}


.botaoIcone {
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 3px;
  border: 1px solid #d9d9d9;
  background-color: transparent;
  width: 19px;
  height: 20px;
  cursor: pointer;
}

.inputForms {
  font-family: 'Roboto', sans-serif;
  height: 50px;
  min-width: 357px;
}

.inputForms :deep(.v-field__field) {
  height: 50px !important;
}

.inputForms :deep(.v-field-label) {
  font-size: 12px !important;
}

.inputForms :deep(input) {
  font-size: 12px !important;
}

.inputForms :deep(.v-field) {
  background-color: transparent !important;
}

.inputForms :deep(.v-field__overlay) {
  background-color: transparent !important;
}

.botaoCinza {
  background-color: #616161;
  display: flex;
  justify-content: center;
  align-items: center;
  width: 357px;
  border-radius: 3px;
  margin-left: 20px;
  margin-right: 20px;
  margin-bottom: 20px;
  font-family: 'Roboto', sans-serif;
  font-size: 14px;
}

.botaoCancelar {
  background-color: #FFFFFF;
  width: 40px;
  height: 40px;
  display: flex;
  margin-left: auto;
  margin-right: 10px;
  margin-top: 8px;
}

.botaoCerteza {
  background-color: #616161;
  color: #FFFFFF;
  height: 40px;
  display: flex;
  justify-content: center;
  align-items: center;
  margin: 0 auto;
  border-radius: 3px;
}

.alignTitleForm {
  color: #424242;
  display: flex;
  justify-content: center;
  align-items: center;
  text-align: center;
  font-size: 24px;
}

.logoForm {
  display: flex;
  justify-content: center;
  align-items: center;
  width: 86px;
  height: 86px;
  padding: 34px;
  margin: 0 auto;
  margin-bottom: 10px;
}

.scroll-area {
  max-height: 250px;
  overflow-y: auto;
}

.descricao {
  display: flex;
  justify-content: center;
  align-items: center;
  font-size: 14px;
  color: #757575;
}
</style>
