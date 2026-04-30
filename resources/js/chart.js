import { Chart, registerables } from "chart.js";
import "./components/chart";

Chart.register(...registerables);
window.Chart = Chart;
