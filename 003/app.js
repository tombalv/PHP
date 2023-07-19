// console.log("Hello World");

let a;
let b;

a = 5;
b = a; // by value (priskyrimas pagal reiksme)

a++;
// console.log(`a:, ${a}, 'b:', ${b}`);

let c;
let d;

c = [5];
d = c; // by reference (priskyrimas pagal nuoroda)
d = [...c]; // copy by value (kopijavimas pagal reiksme)

c.push(6);
d.push(7);

// console.log(`c:, ${c}, 'd:', ${d}`);

let e;
let f;

e = { value: 5, name: "Jonas", animal: "dog" };
f = e; // by reference (priskyrimas pagal nuoroda)

f = { ...e, name: "Simas" }; // copy by value (kopijavimas pagal reiksme)

e.color = "red";

// console.log("e", e, "f:", f);

const g = [1, 2, 3, 4, 5];

const [first, second] = g;

// console.log("first:", first, "second:", second);

const h = { value: 5, name: "Jonas", animal: "dog" };

const { value, name } = h;

// console.log("value:", value, "name:", name);

const house = [
  { id: 5, name: "Jonas", animal: "dog", color: "red", price: 100 },
  { id: 6, name: "Petras", animal: "cat", color: "blue", price: 200 },
  { id: 7, name: "Antanas", animal: "racoon", color: "orange", price: 300 },
  { id: 8, name: "Kazys", animal: "fox", color: "red", price: 400 },
  { id: 9, name: "Ona", animal: "dog", color: "blue", price: 500 },
  { id: 10, name: "Maryte", animal: "cat", color: "orange", price: 600 },
  { id: 11, name: "Kotryna", animal: "racoon", color: "red", price: 700 },
  { id: 12, name: "Kestas", animal: "fox", color: "blue", price: 800 },
];

const newHouse = house.map((m) =>
  m.animal === "cat" ? { ...m, color: "yellow" } : { ...m }
);
const ownDog = house.filter((f) => f.animal === "dog");
const getHouse = house.find((f) => f.animal === "cat");
console.log(getHouse);
