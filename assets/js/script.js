function function_menu() {
  var x = document.getElementById("top_navbar");
  if (x.className === "navbar")
  {
    x.className += " responsive"; // Ajout de la class responsive en version mobile
  }
  else
  {
    x.className = "navbar";
  }
}
