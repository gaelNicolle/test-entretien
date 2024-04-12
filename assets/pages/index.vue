<template>
  <div>
    <div class="mb-2">
      <h1 class="text-4xl font-bold">Bienvenue sur ma belle application</h1>
      <p class="text-xl">Listing des demandes cliniques</p>
    </div>
    <div class="flex gap-2 flex-col w-full">
      <div 
        v-for="depot in depots"
        :key="depot.id"
        class="bg-white rounded-xl shadow-sm p-4"
      >
        <p class="text-base font-semibold">Titre: <span class="text-base text-gray-700 font-light">{{ depot.titre }}</span></p>
        <p class="text-base font-semibold">Description: <span class="text-base text-gray-700 font-light">{{ depot.description }}</span></p>
        <p class="text-base font-semibold">Date de création: <span class="text-base text-gray-700 font-light">{{ depot.date_creation }}</span></p>
        <div class="my-4 p-2 border border-gray rounded-xl bg-gray-100 flex flex-col gap-2" v-if="depot.reponses.length">
          <div :id="'reponse-' + depot.id + '-' + reponse.id" class="border  border-2 bg-white px-4 py-2" 
            :class="{
              'border-green-600': reponse.valide,
              'border-dashed': !reponse.valide,
            }"  
            v-for="reponse in depot.reponses" :key="reponse.id" v-on:click="selectResponse(depot.id, reponse.id, reponse.valide)">
            <p class="text-base font-semibold text-red-500">Type: <span class="text-base text-gray-700 font-light">{{ getTypeLabel(reponse.type) }}</span></p>
            <p class="text-base font-semibold">Titre: <span class="text-base text-gray-700 font-light">{{ reponse.titre }}</span></p>
            <p class="text-base font-semibold">Description: <span class="text-base text-gray-700 font-light">{{ reponse.description }}</span></p>
            <p class="text-base font-semibold">Date de création: <span class="text-base text-gray-700 font-light">{{ reponse.date_creation }}</span></p>
            <p v-if="reponse.valide" class="text-base font-bold"><span class="text-green-500">Reponse Validée</span></p>
          </div>
        </div>
        <div class="flex items-center justify-center" v-else>
          <p class="text-base font-semibold">Aucune réponse</p>
        </div>
        <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mt-2" @click="$router.push(`/depots/${depot.id}`)">Répondre à la demande</button>
        <button v-if="depot.reponses.length" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mt-2" 
          :class="{
            'cursor-not-allowed': !atleastOneSelected(depot.id),
          }"
          :disabled = !atleastOneSelected(depot.id)
          v-on:click="valideReponse(depot.id)" 
        >Valide réponse</button>
      </div>
      <div v-show="showMotif" id="wrapperMotif" class="bg-white rounded-xl shadow-sm p-4 selected">
        <div class="flex items-center justify-center" >
            <label class="block text-gray-700 text-sm font-bold mb-2" for="username">
              Motif de validation
            </label>
            <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="motifValid" type="text" placeholder="Veuillez saisir un motif de validation">
        </div>
        <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mt-2" v-on:click="valideMotifReponse" >Enregistrement motif</button>
      </div>
    </div>
  </div>
</template>

<script>
import { mapActions, mapGetters } from 'vuex';
import { getLabel } from '@/enum/demande_clinique/reponse/type';
import api from '@/api';

export default {
  name: 'Index',
  data: function () {
    return {
      elmSelect: [],
      showMotif: false,
      currentQuestion: null,
    };
  },
  computed: {
    ...mapGetters({
      depots: 'demande_clinique/depots',
    }),
  },
  methods: {
    ...mapActions({
      chargerDepots: 'demande_clinique/chargerDepots',
    }),
    getTypeLabel: getLabel,
    valideReponse: function (idQuestion) {
      this.showMotif = true;
      this.currentQuestion = idQuestion;
      this.$nextTick(() => {
        document.getElementById("wrapperMotif").scrollIntoView();
      });

    }, 
    selectResponse: function (idQuestion, idReponse, isValide) {
      if (!isValide) {
        if (!this.elmSelect[idQuestion]) {
          this.$set(this.elmSelect, idQuestion, []);
        }

        let blocReponse = document.getElementById('reponse-' + idQuestion + '-' + idReponse);
        let index = this.elmSelect[idQuestion].indexOf(idReponse);
        if (index > -1) {
          //il y a un element
          this.elmSelect[idQuestion].splice(index, 1);
          blocReponse.classList.remove("selected");
        } else {
          this.elmSelect[idQuestion].push(idReponse);
          blocReponse.classList.add("selected");
        }
      }
    }, 
    atleastOneSelected: function (idDepot) {
      if (this.elmSelect[idDepot] && Array.isArray(this.elmSelect[idDepot])) {
        return this.elmSelect[idDepot].length > 0;
      }
      
      return false;
    },
    valideMotifReponse: async function () {
      let motif =  document.getElementById('motifValid').value;
      
      if ( !motif ) {
        window.alert('Veuillez remplir tous les champs');
        return;
      }

      try {
        await api.demande_clinique.reponses.validerReponse(this.elmSelect[this.currentQuestion], motif);
        await this.chargerDepots();
        this.elmSelect = [];
        this.showMotif = false;
        this.currentQuestion = null;
      } catch (e) {
        console.error(e);
        window.alert('Une erreur est survenue');
      }
    }, 
  }
};
</script>